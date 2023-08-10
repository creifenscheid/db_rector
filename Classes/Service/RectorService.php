<?php

namespace CReifenscheid\DbRector\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class RectorService implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private const EXT_KEY = 'db_rector';

    protected bool $goodToGo = false;

    protected ?string $version = null;

    protected ?string $rectorPath = null;

    protected ?string $rectorConfiguration = null;

    protected ?string $varFolder = null;

    protected array $rectorSuccessCriteria = [
        '[OK] Rector is done',
        '[OK] 1 file has been changed by Rector',
    ];

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
        // assign state
        $this->goodToGo = $this->version !== null && $this->initRectorConfiguration();
    }

    /**
     * @SeppToDo: The following code feels a bit dirty. Perhaps there is a nicer way to implement it.
     */
    private function initRectorConfiguration(): bool
    {
        if ($this->createVarFolder()) {
            // CONFIGURATION SETUP
            $configurationFilename = 'rector';
            $configurationFile = $this->varFolder . '/' . $configurationFilename . '.php';

            if (file_exists($configurationFile)) {
                $this->rectorConfiguration = $configurationFile;

                return true;
            }

            $configurationTemplate = GeneralUtility::getFileAbsFileName('EXT:' . self::EXT_KEY . '/Resources/Private/Php/' . $configurationFilename . '.tmpl');

            // PREPARE CONFIG FILE
            $configuration = file_get_contents($configurationTemplate);

            // SETUP PHP VERSION FOR RECTOR
            $phpVersion = GeneralUtility::trimExplode('.', PHP_VERSION);
            array_pop($phpVersion);
            $configuration = str_replace('%%PHPVERSION%%', 'PHP_' . implode('', $phpVersion), $configuration);

            // SETUP TYPO3 VERSION
            $typo3version = new Typo3Version();
            $configuration = str_replace(['%%TYPO3CUR%%', '%%TYPO3PREV%%'], ['UP_TO_TYPO3_' . $typo3version->getMajorVersion(), 'UP_TO_TYPO3_' . ($typo3version->getMajorVersion() - 1)], $configuration);

            // WRITE CONFIG FILE
            $fileResult = file_put_contents($configurationFile, $configuration);

            if ($fileResult !== false) {
                $this->rectorConfiguration = $configurationFile;

                return true;
            }

            return false;
        }

        return false;
    }

    private function createVarFolder(): bool
    {
        $varFolder = Environment::getVarPath() . '/' . self::EXT_KEY;

        if (!file_exists($varFolder) && !mkdir($varFolder) && !is_dir($varFolder)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $varFolder));
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

    public function process(string $contentToRefactor): string|bool
    {
        // CREATE TEMP FILE TO RUN RECTOR ON
        $tmpFileName = uniqid() . '.typoscript';
        $tmpFile = $this->varFolder . '/' . $tmpFileName;
        $fileWritten = file_put_contents($tmpFile, $contentToRefactor);

        if ($fileWritten === false) {
            $this->logger->error('The temporary typoscript file could not be written.');

            // write log
            return false;
        }

        $rector = $this->run('process ' . $tmpFile . ' --config ' . $this->rectorConfiguration);

        if ($rector === null) {
            $this->logger->error('An error occurred, so that rector returned "null".');

            return false;
        }

        foreach ($this->rectorSuccessCriteria as $successCriterion) {
            if (str_contains($rector, (string)$successCriterion)) {
                $result = file_get_contents($tmpFile);
                unlink($tmpFile);

                return $result;
            }
        }

        $this->logger->error('Rector could not process the file.', ['return' => $rector]);

        return false;
    }

    public function isShellExecEnabled(): bool
    {
        return is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec');
    }

    private function run(string $statement): ?string
    {
        if ($this->isShellExecEnabled()) {
            return shell_exec($this->rectorPath . $statement);
        }

        return null;
    }
}
