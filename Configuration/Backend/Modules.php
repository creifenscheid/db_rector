<?php

defined('TYPO3') || die();

use CReifenscheid\DbRector\Controller\RectorController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$extKey = 'db_rector';
$moduleIdentifier = GeneralUtility::underscoredToUpperCamelCase($extKey);

return [
    'web_' . $moduleIdentifier => [
        'parent' => 'admin',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'iconIdentifier' => 'db-rector-extension',
        'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => $extKey,
        'workspaces' => 'live',
        'path' => '/module/admin/' . $moduleIdentifier,
        'controllerActions' => [
            RectorController::class => 'index',
        ],
    ],
];
