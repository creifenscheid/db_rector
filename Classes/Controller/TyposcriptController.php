<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Domain\Model\Element;
use CReifenscheid\DbRector\Domain\Repository\ElementRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
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
 * Class TyposcriptController
 */
class TyposcriptController extends BaseController
{

    private const TABLE = 'sys_template';

    protected ?ElementRepository $elementRepository = null;

    public function injectElementRepository(ElementRepository $elementRepository): void
    {
        $this->elementRepository = $elementRepository;
    }

    public function run(): void
    {
        $entries = $this->getDataEntries();

        foreach ($entries as $entry) {
            $this->createModel($entry);
        }

        $elements = $this->elementRepository->findAll();
        $this->view->assign('elements', $elements);
    }

    public function detailAction(Element $element): ResponseInterface
    {
        $this->assignDefaultValues();
        $this->view->assign('element', $element);
        
        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }
    
    public function submitAction(Element $element): ResponseInterface
    {
        $this->updateRectorElement($element, 'typoscript.messages.detail.success.bodytext');

        return $this->redirect('index');
    }

    public function processAllAction(): ResponseInterface
    {
        $elements = $this->elementRepository->findByProcessed(false);
        
        $result = true;
        foreach ($elements as $element) {
            $elementResult = $this->rectorService->process($element->getOriginTyposcript());

            if ($elementResult === false) {
                $result = false;
            } else {
                $element->setProcessedTyposcript($elementResult);
                $element->setProcessed(true);
                try {
                    $this->elementRepository->update($element);
                } catch (IllegalObjectTypeException|UnknownObjectException) {
                    $this->logger->error('The element could not be updated by the repository', ['element' => $element]);
                    $result = false;
                }
            }
        }

        if ($result === false) {
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.processAll.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));
        } else {
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.processAll.success.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::OK));
        }

        $this->elementRepository->persistAll();

        // redirect to index
        return $this->redirect('index');
    }

    public function processAction(Element $element): ResponseInterface
    {
        $rectorResult = $this->rectorService->process($element->getOriginTyposcript());

        if ($rectorResult === false) {
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.general.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));

            return $this->redirect('index');
        }

        $element->setProcessedTyposcript($rectorResult);
        $element->setProcessed(true);

        $this->updateRectorElement($element, 'typoscript.messages.process.success.bodytext');

        return $this->redirect('index');
    }

    public function applyAction(Element $element): ResponseInterface
    {
        $result = $this->updateSysTemplateRecord($element->getOriginUid(), $element->getProcessedTyposcript());

        if (!$result) {
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.general.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));

            $this->logger->warning('Trying to update sys_template:' . $element->getOriginUid() . ' affected 0 rows.', ['element' => $element]);
        }

        $element->setApplied(true);

        $this->updateRectorElement($element, 'typoscript.messages.apply.success.bodytext');

        return $this->redirect('index');
    }

    public function rollBackAction(Element $element): ResponseInterface
    {
        $result = $this->updateSysTemplateRecord($element->getOriginUid(), $element->getOriginTyposcript());

        if (!$result) {
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.general.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));

            $this->logger->warning('Trying to roll back sys_template:' . $element->getOriginUid() . ' affected 0 rows.', ['element' => $element]);
        }

        $element->setApplied(false);

        $this->updateRectorElement($element, 'typoscript.messages.rollBack.success.bodytext');

        return $this->redirect('index');
    }

    public function resetAction(Element $element): ResponseInterface
    {
        try {
            $this->elementRepository->remove($element);
            $this->elementRepository->persistAll();
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.reset.success.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::OK));
        } catch (IllegalObjectTypeException) {
            $this->logger->error('The element could not be removed from the repository', ['element' => $element]);
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.process.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));
        }

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

    private function updateSysTemplateRecord(int $uid, string $typoscript): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $affectedRows = $queryBuilder->update(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->set('config', $typoscript)
            ->executeStatement();

        return $affectedRows !== 0;
    }

    private function updateRectorElement(Element $element, string $messageKey): void
    {
        try {
            $this->elementRepository->update($element);
            $this->elementRepository->persistAll();
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . $messageKey), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::OK));
        } catch (IllegalObjectTypeException|UnknownObjectException) {
            $this->logger->error('The element could not be updated by the repository', ['element' => $element]);
            $this->addFlashMessage(LocalizationUtility::translate(self::L10N . 'typoscript.messages.process.error.bodytext'), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . AbstractMessage::ERROR));
        }
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
