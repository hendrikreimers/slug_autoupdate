<?php
namespace K23\SlugAutoupdate\Hooks\TceMain;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use K23\SlugAutoupdate\Service\SlugService;

class MoveRecordHook
{
    /**
     * @var \K23\SlugAutoupdate\Service\SlugService
     */
    protected $slugService = null;

    /**
     * MoveRecord Hook under UID = 0
     *
     * @param $table
     * @param $uid
     * @param $destPid
     * @param $moveRec
     * @param $updateFields
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function moveRecord_firstElementPostProcess($table, $uid, $destPid, $moveRec, $updateFields, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj) {
        // Take care that it's only done after a page has been moved
        if ( ($table === 'pages') && ($uid > 0) ) {
            $this->getSlugService()->updateSlugRecursive($table, $uid);
        }
    }

    /**
     * Move Record under UID > 0
     * @param $table
     * @param $uid
     * @param $destPid
     * @param $origDestPid
     * @param $moveRec
     * @param $updateFields
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function moveRecord_afterAnotherElementPostProcess($table, $uid, $destPid, $origDestPid, $moveRec, $updateFields, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj) {
        // Take care that it's only done after a page has been moved
        if ( ($table === 'pages') && ($uid > 0) ) {
            $this->getSlugService()->updateSlugRecursive($table, $uid);
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