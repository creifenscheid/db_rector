<?php

$extensionKey = 'db_rector';

$table = \basename(__FILE__, '.php');
$l10n = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/TCA/locallang_' . $table . '.xlf:';

return [
    'ctrl' => [
        'hideTable' => true,
        'title' => $l10n . 'label',
        'label' => 'origin_title',
        'label_alt' => 'origin_uid',
        'label_alt_force' => true,
        'adminOnly' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'origUid' => 't3_origuid',

        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l10n_source',

        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],

        'iconfile' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/Extension.svg',
        'search' => 'origin_uid, origin_title, origin_typoscript, processed_typoscript',
    ],
    'types' => [
        [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;origin,
                --div--;' . $l10n . 'tab.refactor,
                    --palette--;;process,processed_typoscript,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,hidden,
                    --palette--;;timeRestrictions,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,sys_language_uid
            ',
        ],
    ],
    'palettes' => [
        'origin' => [
            'showitem' => 'origin_uid, origin_pid, --linebreak--, origin_title, --linebreak--, origin_typoscript',
        ],
        'process' => [
            'showitem' => 'processed, applied',
        ],
        'timeRestrictions' => [
            'showitem' => 'starttime,endtime',
        ],
    ],
    'columns' => [
        'tstamp' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.tstamp',
            'config' => [
                'default' => '',
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'readOnly' => true,
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => $table,
                'foreign_table_where' =>
                    'AND {#' . $table . '}.{#pid}=###CURRENT_PID###'
                    . ' AND {#' . $table . '}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'origin_uid' => [
            'label' => $l10n . 'origin_uid',
            'config' => [
                'default' => '',
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim, num',
                'readOnly' => true,
            ],
        ],
        'origin_pid' => [
            'label' => $l10n . 'origin_pid',
            'config' => [
                'default' => '',
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim, num',
                'readOnly' => true,
            ],
        ],
        'origin_title' => [
            'label' => $l10n . 'origin_title',
            'config' => [
                'default' => '',
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
        'origin_typoscript' => [
            'label' => $l10n . 'origin_typoscript',
            'config' => [
                'default' => '',
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'readOnly' => true,
            ],
        ],
        'processed_typoscript' => [
            'label' => $l10n . 'processed_typoscript',
            'config' => [
                'default' => '',
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'readOnly' => true,
            ],
        ],
        'processed' => [
            'label' => $l10n . 'processed',
            'config' => [
                'default' => false,
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'readOnly' => true,
            ],
        ],
        'applied' => [
            'label' => $l10n . 'applied',
            'config' => [
                'default' => false,
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'readOnly' => true,
            ],
        ],
    ],
];
