<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add SlugAutoUpdate field TCA
(function() {
    // Basics
    $extKey    = 'slug_autoupdate';
    $fieldName = 'slug_autoupdate';
    $table     = 'pages';

    // Define new field
    $temporaryTableConfigurationArray = [
        $fieldName => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/tca.xlf:' . $fieldName,
            'config'  => array(
                'type' => 'check',
                'default' => 1
            ),
            'onChange' => 'reload'
        ]
    ];

    // Register new TCA columns
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        $table,
        $temporaryTableConfigurationArray
    );

    // Add to pages after slug field to all types
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        $fieldName,
        '',
        'after:slug'
    );
})();