<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Typo3LevelSetList::UP_TO_TYPO3_10,
        Typo3LevelSetList::UP_TO_TYPO3_11,
    ]);
    $rectorConfig->phpVersion(PhpVersion::%%PHPVERSION%%);
};
