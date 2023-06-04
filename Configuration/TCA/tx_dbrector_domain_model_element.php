<?php

$extensionKey = 'db_rector';

$table = basename(__FILE__, '.php');
$l10n = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/TCA/locallang_' . $table . '.xlf:';

return [
    'ctrl' => [
        'hideTable' => true,
        'title' => $l10n . 'label',
        'label' => '',
        'adminOnly' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'origUid' => 't3_origuid',
        'sorting' => 'sorting',

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
        'search' => '',
    ],
    'types' => [
        [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'origin' => [
            'label' => $l10n . 'origin',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'original_data' => [
            'label' => $l10n . 'original_data',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'default' => '',
                'readOnly' => true,
            ],
        ],
        'refactored_data' => [
            'label' => $l10n . 'refactored_data',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'default' => '',
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
