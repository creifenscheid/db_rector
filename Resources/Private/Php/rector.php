<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Typo3LevelSetList::%%TYPO3PREV%%,
        Typo3LevelSetList::%%TYPO3CUR%%,
    ]);
    $rectorConfig->phpVersion(PhpVersion::%%PHPVERSION%%);
};
