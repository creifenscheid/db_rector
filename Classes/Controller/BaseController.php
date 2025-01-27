<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Configuration\ExtensionConfiguration;
use CReifenscheid\DbRector\Service\RectorService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use ReflectionClass;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

use function method_exists;
use function strtolower;

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
class BaseController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected const string L10N = 'LLL:EXT:db_rector/Resources/Private/Language/locallang_mod.xlf:';

    protected string $shortName = '';

    protected ?Typo3Version $typo3Version = null;

    protected bool $restrictedRendering = true;

    protected ?ModuleTemplate $moduleTemplate = null;

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly PageRenderer $pageRenderer,
        protected readonly ConnectionPool $connectionPool,
        protected readonly ExtensionConfiguration $extensionConfiguration,
        protected readonly RectorService $rectorService
    ) {
        $reflect = new ReflectionClass($this);
        $this->shortName = $reflect->getShortName();

        $this->typo3Version = new Typo3Version();

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    protected function initializeModuleTemplate(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        // generate the dropdown menu
        $this->buildMenu($this->shortName);
    }

    protected function buildMenu(string $currentController): void
    {
        $this->uriBuilder->setRequest($this->request);


        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier($this->request->getControllerExtensionName() . 'ModuleMenu');

        $moduleControllerActions = $this->request->getAttribute('module')->getControllerActions();

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

    protected function assignDefaultValues(): void
    {
        $this->moduleTemplate->assignMultiple([
            'l10n' => self::L10N,
            'contextIsDevelopment' => Environment::getContext()->isDevelopment(),
            'ignoreTYPO3Context' => $this->extensionConfiguration->getIgnoreTYPO3Context(),
            'composerMode' => Environment::isComposerMode(),
            'restrictedRendering' => $this->restrictedRendering,
            'rector' => $this->rectorService->getGoodToGo(),
            'typo3version' => $this->typo3Version->getMajorVersion(),
        ]);
    }
}
