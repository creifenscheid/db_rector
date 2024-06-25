<?php

$extensionKey = 'db_rector';

return [
    'imports' => [
        '@creifenscheid/' . \str_replace('_', '-', $extensionKey) . '/' => 'EXT:' . $extensionKey . '/Resources/Public/JavaScript/',
    ],
];
