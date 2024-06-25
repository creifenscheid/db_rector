<?php

defined('TYPO3') || die();

(static function ($extKey) {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets'][$extKey] = 'EXT:' . $extKey . '/Resources/Public/Css/DbRector.css';
})('db_rector');
