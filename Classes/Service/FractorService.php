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

class FractorService implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private const EXT_KEY = 'db_rector';

    protected bool $goodToGo = false;

    protected ?string $version = null;

    protected ?string $fractorPath = null;

    protected ?string $fractorConfiguration = null;

    protected ?string $varFolder = null;

    protected array $fractorSuccessCriteria = [
        '[OK] Fractor is done',
        '[OK] 1 file has been changed by Fractor',
    ];

    public function __construct()
    {
        $this->fractorPath = Environment::getProjectPath() . '/vendor/bin/fractor ';
        $this->init();
    }

    private function init(): void
    {
        // fractor always needs a valid configuration file, even for a '--version' call
        $configurationInitialized = $this->initFractorConfiguration();

        $this->version = $this->run('--version');

        $versionPattern = '/^Fractor \d+\.\d+\.\d+$/';
        $this->goodToGo = preg_match($versionPattern, $this->version) && $configurationInitialized;
    }

    /**
     * @SeppToDo: The following code feels a bit dirty. Perhaps there is a nicer way to implement it.
     */
    private function initFractorConfiguration(): bool
    {
        if ($this->createVarFolder()) {
            // CONFIGURATION SETUP
            $configurationFilename = 'fractor';
            $configurationFile = $this->varFolder . '/' . $configurationFilename . '.php';

            if (\file_exists($configurationFile)) {
                $this->fractorConfiguration = $configurationFile;

                return true;
            }

            $configurationTemplate = GeneralUtility::getFileAbsFileName('EXT:' . self::EXT_KEY . '/Resources/Private/Php/' . $configurationFilename . '.tmpl');

            // PREPARE CONFIG FILE
            $configuration = \file_get_contents($configurationTemplate);

            // SETUP PHP VERSION FOR FRACTOR
            $phpVersion = GeneralUtility::trimExplode('.', PHP_VERSION);
            \array_pop($phpVersion);
            $configuration = str_replace('%%PHPVERSION%%', 'PHP_' . \implode('', $phpVersion), $configuration);

            // SETUP TYPO3 VERSION
            $typo3version = new Typo3Version();
            $configuration = \str_replace(['%%TYPO3CUR%%', '%%TYPO3PREV%%'], ['UP_TO_TYPO3_' . $typo3version->getMajorVersion(), 'UP_TO_TYPO3_' . ($typo3version->getMajorVersion() - 1)], $configuration);

            // WRITE CONFIG FILE
            $fileResult = \file_put_contents($configurationFile, $configuration);

            if ($fileResult !== false) {
                $this->fractorConfiguration = $configurationFile;

                return true;
            }

            return false;
        }

        return false;
    }

    private function createVarFolder(): bool
    {
        $varFolder = Environment::getVarPath() . '/' . self::EXT_KEY;

        if (!\file_exists($varFolder) && !\mkdir($varFolder) && !\is_dir($varFolder)) {
            throw new RuntimeException(\sprintf('Directory "%s" was not created', $varFolder), 9547825830);
        }

        if (!\is_writable($varFolder)) {
            return \chmod($varFolder, $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask']);
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
        // CREATE TEMP FILE TO RUN FRACTOR ON
        $tmpFileName = \uniqid() . '.typoscript';
        $tmpFile = $this->varFolder . '/' . $tmpFileName;
        $fileWritten = \file_put_contents($tmpFile, $contentToRefactor);

        if ($fileWritten === false) {
            $this->logger->error('The temporary typoscript file could not be written.');

            // write log
            return false;
        }

        $fractor = $this->run('process ');

        if ($fractor === null) {
            $this->logger->error('An error occurred, so that fractor returned "null".');

            return false;
        }

        foreach ($this->fractorSuccessCriteria as $successCriterion) {
            if (\str_contains($fractor, (string)$successCriterion)) {
                $result = \file_get_contents($tmpFile);
                \unlink($tmpFile);

                return $result;
            }
        }

        $this->logger->error('Fractor could not process the file.', ['return' => $fractor]);

        return false;
    }

    public function isShellExecEnabled(): bool
    {
        return is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec');
    }

    private function run(string $statement): ?string
    {
        return \shell_exec($this->fractorPath . $statement . ' --config ' . $this->fractorConfiguration);
    }
}
