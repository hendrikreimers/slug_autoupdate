<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Register on save hook
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['slug_autoupdate'] =
    \K23\SlugAutoupdate\Hooks\TceMain\AfterSaveHook::class;

// Register move record hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass']['slug_autoupdate'] =
    \K23\SlugAutoupdate\Hooks\TceMain\MoveRecordHook::class;
