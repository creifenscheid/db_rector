<?php

namespace CReifenscheid\DbRector\Configuration;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
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
 * Class ExtensionConfiguration
 */
class ExtensionConfiguration
{
    private const EXTENSION_KEY = 'db_rector';
    protected bool $ignoreTYPO3Context = false;

    public function __construct()
    {
        try {
            $extensionConfiguration = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get(self::EXTENSION_KEY);

            if (is_array($extensionConfiguration)) {
                foreach ($extensionConfiguration as $key => $value) {
                    $methodName = 'set' . ucfirst($key);
                    if (method_exists($this, $methodName)) {
                        $this->$methodName($value);
                    }
                }
            }
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException $e) {
        }
    }

    public function getIgnoreTYPO3Context(): bool
    {
        return $this->ignoreTYPO3Context;
    }

    public function setIgnoreTYPO3Context(string $ignoreTYPO3Context): void
    {
        $this->ignoreTYPO3Context = filter_var($ignoreTYPO3Context, FILTER_VALIDATE_BOOLEAN);
    }
}
