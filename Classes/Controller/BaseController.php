<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Configuration\ExtensionConfiguration;
use CReifenscheid\DbRector\Service\RectorService;
use ReflectionClass;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
class BaseController extends ActionController implements RectorControllerInterface
{
    protected string $shortName = '';
    protected ?ExtensionConfiguration $extensionConfiguration = null;
    protected ?RectorService $rectorService = null;
    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected ModuleTemplate $moduleTemplate;
    protected PageRenderer $pageRenderer;
    protected ?ConnectionPool $connectionPool = null;
    protected ?Typo3Version $typo3Version = null;
    protected bool $restrictedRendering = true;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        PageRenderer $pageRenderer,
        ConnectionPool $connectionPool,
        ExtensionConfiguration $extensionConfiguration,
        RectorService $rectorService
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;
        $this->connectionPool = $connectionPool;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->rectorService = $rectorService;

        $reflect = new ReflectionClass($this);
        $this->shortName = $reflect->getShortName();

        $this->typo3Version = new Typo3Version();
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
        $menu->setIdentifier($this->request->getControllerExtensionName() . 'ModuleMenu');

        if ($this->typo3Version->getMajorVersion() < 12) {
            $moduleControllerActions = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$this->request->getControllerExtensionName()]['modules'][$this->request->getPluginName()]['controllers'];
        } else {
            $moduleControllerActions = $this->request->getAttribute('module')->getControllerActions();
        }

        foreach ($moduleControllerActions as $configuredController) {
            $alias = $configuredController['alias'];

            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle(LocalizationUtility::translate('LLL:EXT:' . GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName()) . '/Resources/Private/Language/locallang_mod.xlf:section.' . strtolower((string)$alias)))
                    ->setHref($this->uriBuilder->uriFor('index', null, $alias))
                    ->setActive($currentController === $alias . 'Controller')
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->assignDefaultValues();

        if (Environment::isComposerMode() && ($this->extensionConfiguration->getIgnoreTYPO3Context() === true || Environment::getContext()->isDevelopment())) {
            $this->run();
        }

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    public function run(): void
    {
    }

    protected function assignDefaultValues(): void
    {
        $this->view->assignMultiple([
            'l10n' => 'LLL:EXT:db_rector/Resources/Private/Language/locallang_mod.xlf:',
            'contextIsDevelopment' => Environment::getContext()->isDevelopment(),
            'ignoreTYPO3Context' => $this->extensionConfiguration->getIgnoreTYPO3Context(),
            'composerMode' => Environment::isComposerMode(),
            'restrictedRendering' => $this->restrictedRendering,
            'rector' => $this->rectorService->getGoodToGo(),
        ]);
    }
}
