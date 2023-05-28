<?php

namespace CReifenscheid\DbRector\Controller;

use ReflectionClass;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
 * Class BaseController
 */
class BaseController extends ActionController
{
    protected ModuleTemplateFactory $moduleTemplateFactory;

    protected ModuleTemplate $moduleTemplate;

    protected PageRenderer $pageRenderer;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        PageRenderer $pageRenderer
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;

        $reflect = new ReflectionClass($this);
        $this->shortName = $reflect->getShortName();
    }

    /**
     * @throws NoSuchArgumentException
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        // generate the dropdown menu
        $this->buildMenu($this->shortName);
    }

    protected function buildMenu(string $currentController): void
    {
        $this->uriBuilder->setRequest($this->request);

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('DbRectorModuleMenu');

        $moduleControllerActions = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$this->request->getControllerExtensionName()]['modules'][$this->request->getPluginName()]['controllers'];

        foreach ($moduleControllerActions as $configuredController) {
            $alias = $configuredController['alias'];

            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:db_rector/Resources/Private/Language/locallang_mod.xlf:section.' . strtolower((string)$alias)))
                    ->setHref($this->uriBuilder->uriFor('index', null, $alias))
                    ->setActive($currentController === $alias . 'Controller')
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }
}
