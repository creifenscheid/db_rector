<?php

namespace CReifenscheid\DbRector\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2023 Christian Reifenscheid
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class RectorService
 */
class RectorService implements SingletonInterface
{
    private const EXT_KEY = 'db_rector';
    protected bool $goodToGo = false;
    protected ?string $version = null;
    protected ?string $rectorPath = null;
    protected ?string $varFolder = null;

    public function __construct()
    {
        $this->rectorPath = Environment::getProjectPath() . '/vendor/bin/rector ';
        $this->init();
    }

    private function init(): void
    {
        $this->version = $this->run('--version');

        /*
         * the shell returns either a string containing the version number or null
         * null as return means, that an error occurred
         * and an error means, rector does not run properly or there is a problem with running rector
         * so, we are not good to go.
         */
        if ($this->version === null) {
            $goodToGo = false;
        } else {
            $goodToGo = $this->initRectorConfiguration();
        }

        // assign state
        $this->goodToGo = $goodToGo;
    }

    /**
     * @SeppToDo: The following code feels a bit dirty. Perhaps there is a nicer way to implement it.
     */
    private function initRectorConfiguration(): bool
    {
        if ($this->createVarFolder() !== false) {
            // CONFIGURATION SETUP
            $configurationFilename = 'rector.php';
            $configurationFile = $this->varFolder . '/' . $configurationFilename;
            $configurationTemplate = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:' . self::EXT_KEY . '/Resources/Private/Php/' . $configurationFilename);

            if (file_exists($configurationFile)) {
                return true;
            }

            // PREPARE CONFIG FILE
            $configuration = file_get_contents($configurationTemplate);

            // SETUP PHP VERSION FOR RECTOR
            $phpVersion = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('.', PHP_VERSION);
            array_pop($phpVersion);
            $configuration = str_replace('%%PHPVERSION%%', 'PHP_' . implode('', $phpVersion), $configuration);

            // SETUP TYPO3 VERSION
            $typo3version = new Typo3Version();
            $configuration = str_replace('%%TYPO3CUR%%', 'UP_TO_TYPO3_' . $typo3version->getMajorVersion(), $configuration);
            $configuration = str_replace('%%TYPO3PREV%%', 'UP_TO_TYPO3_' . ($typo3version->getMajorVersion() - 1), $configuration);

            // WRITE CONFIG FILE
            $fileResult = file_put_contents($configurationFile, $configuration);

            return !($fileResult === false);
        }

        return false;
    }

    private function createVarFolder(): bool
    {
        $varFolder = Environment::getVarPath() . '/' . self::EXT_KEY;

        if (!file_exists($varFolder) && !mkdir($varFolder) && !is_dir($varFolder)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $varFolder));
        }

        if (!is_writable($varFolder)) {
            return chmod($varFolder, $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask']);
        }

        $this->varFolder = $varFolder;

        return true;
    }

    public function getGoodToGo(): bool
    {
        return $this->goodToGo;
    }

    public function getVersion(): bool|string
    {
        return $this->goodToGo ? $this->version : $this->goodToGo;
    }

    private function run(string $statement): ?string
    {
        return shell_exec($this->rectorPath . $statement);
    }
}
