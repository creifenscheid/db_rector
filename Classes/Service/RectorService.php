<?php

namespace CReifenscheid\DbRector\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Core\Environment;

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
    protected bool $goodToGo = false;
    protected ?string $version = null;
    protected ?string $path = null;
    
    public function __construct()
    {
        $this->path = Environment::getProjectPath() . '/vendor/bin/rector ';
        
        $this->version = $this->run('--version');
        
        /*
         * the shell returns either a string containing the version number or null
         * null as return means, that an error occured
         * and an error means, rector does not run properly or their is a problem with running rector
         * so, we are not good to go.
         */
        if (is_string($this->version)) {
            $this->goodToGo = true;
        }
    }

    public function getGoodToGo(): bool
    {
        return $this->goodToGo;
    }

// SeppToDo: check php version for return statement
// set needed pho version as requirement
    public function getVersion(): bool|string
    {
         return $this->goodToGo ? $this->version : $this->goodToGo;
    }
    
    private function run(string $statement): ?string 
    {
         return shell_exec($this->path . $statement);
    }
}
