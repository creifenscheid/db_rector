<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

(static function ($extKey) {
    $typo3Version = new Typo3Version();

    if ($typo3Version->getMajorVersion() < 12) {
        ExtensionUtility::registerModule(
            \ucfirst(GeneralUtility::underscoredToLowerCamelCase($extKey)),
            'tools',
            $extKey,
            'after:toolsupgrade',
            [
                \CReifenscheid\DbRector\Controller\TyposcriptController::class => 'index, detail, processAll, processSelection, process, apply, rollBack, submit, reset',
                \CReifenscheid\DbRector\Controller\SetupController::class => 'index',
            ],
            [
                'access' => 'admin',
                'iconIdentifier' => 'db-rector-extension',
                'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_mod.xlf',
            ]
        );

        // SKIN
        $GLOBALS['TBE_STYLES']['skins'][$extKey] = [
            'name' => 'DB Rector',
            'stylesheetDirectories' => [
                'css' => 'EXT:' . $extKey . '/Resources/Public/Css/',
            ],
        ];
    }
})('db_rector');
