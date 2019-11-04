<?php
namespace K23\SlugAutoupdate\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;

/**
 * Class SlugService
 * @package K23\Siteprovider\Service\SlugService
 *
 * Use in TCEmain Hook /Hooks/AfterSafeHook.php
 */
class SlugService
{
    /**
     * @var \TYPO3\CMS\Core\DataHandling\SlugHelper
     */
    private $slugHelper = null;

    /**
     * Updates slug fields recursively
     *
     * @param string $table
     * @param int $id
     * @param int|null $pid
     * @return void
     */
    public function updateSlugRecursive($table, $id, $pid = null)
    {
        // Get current page record
        $queryBuilder = $this->getQueryBuilder();
        $pageRecord   = $queryBuilder
            ->select('*')
            ->from($table)
            ->orderBy('pid', 'ASC')
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAll()[0];

        // Update slug record
        if ( boolval($pageRecord['slug_autoupdate']) === true ) {
            $newSlug = $this->buildSlug($pageRecord, $pid);

            // UPDATE
            $queryBuilder = $this->getQueryBuilder();
            $queryBuilder
                ->update($table)
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageRecord['uid'], \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('slug_autoupdate', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
                )
                ->set('slug', $newSlug)
                ->execute();
        }

        // Check if has subpages
        $queryBuilder = $this->getQueryBuilder();
        $subpages = $queryBuilder
            ->select('uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAll();

        if ( sizeof($subpages) > 0 ) {
            foreach ( $subpages as $subpageUid ) {
                $this->updateSlugRecursive($table, $subpageUid['uid']);
            }
        }
    }

    /**
     * Build a unique Slug URI
     *
     * @param $record
     * @param int|null $pid
     * @param string $table
     * @param array|null $fieldConfig
     * @param string $slugFieldName
     * @return string
     */
    public function buildSlug($record, $pid = null, $table = 'pages', $fieldConfig = null, $slugFieldName = 'slug')
    {
        $pid  = ( $pid !== null ) ? (int)$pid : (int)$record['pid'];
        $slug = $this->getSlugHelper($table)->generate($record, $pid);

        if ( $fieldConfig === null ) {
            $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$slugFieldName]['config'];
            $state = RecordStateFactory::forName($table)->fromArray($record, $pid);
        }

        if ( strstr($fieldConfig['eval'], 'uniqueInSite') ) {
            $slug = $this->getSlugHelper($table)->buildSlugForUniqueInSite($slug, $state);
        }

        if ( strstr($fieldConfig['eval'], 'uniqueInPid') ) {
            $slug = $this->getSlugHelper($table)->buildSlugForUniqueInPid($slug, $state);
        }

        $slug = $this->getSlugHelper($table)->sanitize($slug);

        return $slug;
    }

    /**
     * Initiates and returns the SlugHelper
     *
     * @param $table
     * @param string $routeFieldName
     * @param string $slugFieldName
     * @param null $fieldConfig
     * @return SlugHelper
     */
    private function getSlugHelper($table, $routeFieldName = 'slug', $slugFieldName = 'slug', $fieldConfig = null)
    {
        // Make instance if not already done
        if ( $this->slugHelper === null ) {
            if ( $fieldConfig === null ) {
                $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$slugFieldName]['config'];
            }

            $this->slugHelper = GeneralUtility::makeInstance(
                SlugHelper::class,
                $table,
                $routeFieldName,
                $fieldConfig
            );
        }

        return $this->slugHelper;
    }

    /**
     * Returns a QueryBuilder instance
     *
     * @param string $table
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    private function getQueryBuilder($table = 'pages')
    {
        return GeneralUtility::makeInstance(
            ConnectionPool::class
        )->getQueryBuilderForTable($table);
    }
}