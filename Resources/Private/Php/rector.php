<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

/**
 * WELCOME TO THE RECTOR CONFIGURATION FILE
 *
 * Please note the following hints to be prepared for problems, results etc.
 *
 *    1. Warnings
 *       When you use rector there are rules that require some more actions like creating UpgradeWizards for outdated TCA types.
 *       To fully support you we added some warnings, so watch out for them.
 *
 *    2. Extbase Persistence Config file
 *       Watch out for a file called "Configuration_Extbase_Persistence_Classes.php" on projects root level.
 *       This files contains the corresponding configuration, taken from the typoscript equivalent.
 *       Rename and relocate the file to the extension.
 *
 *    3. Extension icon
 *       If there is no icon called "Extension" within "Resources/Public/Icons", the file "ext_icon" will be copied over and renamed.
 *       "ext_icon" on extension root level will not be removed automatically, so that is your part.
 *
 *    4. Final check
 *       After rector is done doing its magic, check on every change (if you did not run in dry-mode).
 *       Don't rely on rector understanding your code correctly, leading to the correct result.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Typo3LevelSetList::UP_TO_TYPO3_10,
        Typo3LevelSetList::UP_TO_TYPO3_11,
    ]);
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
};
