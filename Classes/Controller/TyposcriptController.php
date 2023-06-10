<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Domain\Model\Element;
use CReifenscheid\DbRector\Domain\Repository\ElementRepository;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

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
    /**
     * @var string
     */
    private const TABLE = 'sys_template';

    protected ?ElementRepository $elementRepository = null;

    public function injectElementRepository(ElementRepository $elementRepository): void
    {
        $this->elementRepository = $elementRepository;
    }

    public function run(): void
    {
        //$filePath = Environment::getVarPath() . '/' . GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName());
        //$return = shell_exec(Environment::getProjectPath() . '/vendor/bin/rector process ' . $filePath . '/db_rector.typoscript' . ' --config ' . $filePath . '/rector.php');

        $entries = $this->getDataEntries();

        foreach ($entries as $entry) {
            $this->createModel($entry);
        }

        $elements = $this->elementRepository->findAll();
        $this->view->assign('elements', $elements);

        // implement toolbar
        // run rector on entry
        // apply rector result to original entry
    }

    public function processAllAction(): \Psr\Http\Message\ResponseInterface
    {
        $messagePrefix = 'LLL:EXT:db_rector/Resources/Private/Language/locallang_mod.xlf:typoscript.message.processAll';
        $this->addFlashMessage(LocalizationUtility::translate($messagePrefix . '.bodytext'), LocalizationUtility::translate($messagePrefix . '.header.' . AbstractMessage::OK));

        // redirect to index
        return $this->redirect('index');
    }

    private function getDataEntries(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->select('uid', 'pid', 'title', 'config')
        ->from(self::TABLE)
        ->where(
            $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0)),
        );

        try {
            return $queryBuilder->executeQuery()->fetchAllAssociative();
        } catch (Exception) {
        }

        return [];
    }

    private function createModel(array $data): void
    {
        // CHECK IF MODEL ALREADY EXISTS
        $existingModel = $this->elementRepository->findByOriginUid($data['uid']);
        if ($existingModel instanceof QueryResult && $existingModel->count() > 0) {
            return;
        }
        if (is_array($existingModel) && $existingModel !== []) {
            return;
        }

        // SET UP MODEL
        $element = new Element();
        $element
            ->setOriginUid($data['uid'])
            ->setOriginPid($data['pid'])
            ->setOriginTitle($data['title'])
            ->setOriginTyposcript($data['config']);

        try {
            $this->elementRepository->add($element);
            $this->elementRepository->persistAll();
        } catch (IllegalObjectTypeException) {
        }
    }
}
