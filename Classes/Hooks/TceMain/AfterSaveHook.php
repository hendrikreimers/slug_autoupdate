<?php
namespace K23\SlugAutoupdate\Hooks\TceMain;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use K23\SlugAutoupdate\Service\SlugService;

class AfterSaveHook
{
    /**
     * @var \K23\SlugAutoupdate\Service\SlugService
     */
    protected $slugService = null;

    /**
     * Altered actions after record is processed in the DB
     *
     * @param $status
     * @param $table
     * @param $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        // Take care thats only done if its an update of a page
        if ( ($status === 'update') && ($table === 'pages') && ($id > 0) ) {
            // Get slug generator fields plus the slug fields itself
            $slugGenFields = array_merge(
                $GLOBALS['TCA']['pages']['columns']['slug']['config']['generatorOptions']['fields'],
                ['slug', 'slug_autoupdate']
            );

            // Regenerate slug only if the given field changed
            if ( !empty(array_intersect(array_keys($fieldArray), $slugGenFields)) ) {
                $this->getSlugService()->updateSlugRecursive($table, $id);
            }
        }
    }

    /**
     * Returns the SlugService instance
     *
     * @return SlugService
     */
    private function getSlugService()
    {
        if ( $this->slugService === null ) {
            $this->slugService = GeneralUtility::makeInstance(
                SlugService::class
            );
        }

        return $this->slugService;
    }
}