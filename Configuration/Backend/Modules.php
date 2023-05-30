<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;

$extKey = 'db_rector';
$moduleIdentifier = GeneralUtility::underscoredToUpperCamelCase($extKey);

return [
    'web_' . $moduleIdentifier => [
        'parent' => 'web',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'iconIdentifier' => 'db-rector-extension',
        'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => $extKey,
        'workspaces' => 'live',
        'path' => '/module/admin/' . $moduleIdentifier,
        'controllerActions' => [
            \CReifenscheid\DbRector\Controller\RectorController::class => 'index',
        ],
    ],
];
