<?php

namespace CReifenscheid\DbRector\Controller;

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
 * Class TyposcriptController
 */
class TyposcriptController extends BaseController
{
    public function run(): void
    {
        //$filePath = Environment::getVarPath() . '/' . GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName());
        //$return = shell_exec(Environment::getProjectPath() . '/vendor/bin/rector process ' . $filePath . '/db_rector.typoscript' . ' --config ' . $filePath . '/rector.php');

        // get all sys_template entries
        // create rector models
        // show models in view
        // implement toolbar
            // run rector on entry
            // apply rector result to original entry
    }

    private function getDataEntries(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        $queryBuilder->select('uid', 'constants', 'config')

        return[];
    }
}
