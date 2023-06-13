<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;

$extKey = 'db_rector';
$moduleIdentifier = GeneralUtility::underscoredToUpperCamelCase($extKey);

return [
    'web_' . $moduleIdentifier => [
        'parent' => 'tools',
        'position' => ['after' => 'tools_toolsupgrade'],
        'access' => 'admin',
        'iconIdentifier' => 'db-rector-extension',
        'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => $extKey,
        'workspaces' => 'live',
        'path' => '/module/tools/' . $moduleIdentifier,
        'controllerActions' => [
            \CReifenscheid\DbRector\Controller\TyposcriptController::class => 'index, detail, processAll, process, apply, rollBack, submit, reset',
            \CReifenscheid\DbRector\Controller\SetupController::class => 'index',
        ],
    ],
];
