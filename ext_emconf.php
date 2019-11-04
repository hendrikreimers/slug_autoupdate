<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "siteprovider"
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'SLUG Auto-Update',
    'description' => 'Automaticly updates the SLUG field in pages, after editing the title or moving the page.',
    'category' => 'distribution',
    'author' => 'Hendrik Reimers (KERN23)',
    'author_email' => 'info@core23.com',
    'author_company' => 'CORE23.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'autoload' => [
        'psr-4' => [
            'K23\\SlugAutoupdate\\' => 'Classes'
        ],
    ],
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.0-9.5.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);