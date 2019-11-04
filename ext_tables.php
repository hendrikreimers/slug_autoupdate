<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Change SLUG Field behaviour based on slug_autoupdate field (Show field only if autoupdate disabled)
$GLOBALS['TCA']['pages']['columns']['slug']['displayCond'] = 'FIELD:slug_autoupdate:REQ:false';