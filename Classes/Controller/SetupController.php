<?php

namespace CReifenscheid\DbRector\Controller;

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

class SetupController extends BaseController
{
    protected bool $restrictedRendering = false;

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->initializeModuleTemplate();
        $this->assignDefaultValues();

        $this->moduleTemplate->assignMultiple([
            'setup' => [
                'typo3version' => $this->typo3Version->getVersion(),
                'typo3context' => Environment::getContext(),
                'ignoreTypo3context' => $this->extensionConfiguration->getIgnoreTYPO3Context(),
                'composer' => Environment::isComposerMode(),
                'fractorVersion' => $this->fractorService->getVersion(),
                'phpVersion' => PHP_VERSION,
                'shellExec' => $this->fractorService->isShellExecEnabled(),
            ],
        ]);

        return $this->moduleTemplate->renderResponse('Setup/Index');
    }
}
