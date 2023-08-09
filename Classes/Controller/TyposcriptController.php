<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Domain\Model\Element;
use CReifenscheid\DbRector\Domain\Repository\ElementRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\DiffUtility;

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

    private ?DataHandler $dataHandler = null;

    private bool $logErrorOccurred = false;

    private bool $processingError = false;

    private bool $stackProcess = false;

    public function injectElementRepository(ElementRepository $elementRepository): void
    {
        $this->elementRepository = $elementRepository;
    }

    // SeppToDo: TEST
    public function __destruct()
    {
        if ($this->logErrorOccurred) {
            $messageKey = $this->stackProcess ? 'typoscript.messages.stackLog.error.bodytext' : 'typoscript.messages.log.error.bodytext';
            $this->setupFlashMessage($messageKey, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }

        if ($this->processingError) {
            $messageKey = $this->stackProcess ? 'typoscript.messages.stackProcess.error.bodytext' : 'typoscript.messages.process.error.bodytext';
            $this->setupFlashMessage($messageKey, \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
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
         
        if($element->getProcessedTyposcript() !== '') {
            $diffUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DiffUtility::class);

            $diff = [];
            $origin = GeneralUtility::trimExplode(PHP_EOL, $element->getOriginTyposcript());
            $processed = GeneralUtility::trimExplode(PHP_EOL, $element->getProcessedTyposcript());

            for($i = 0, $imax = count($origin); $i < $imax; ++$i) {
                $diff[] = $diffUtility->makeDiffDisplay($origin[$i], $processed[$i]);
            }

            $this->view->assign('diff', implode('<br>', $diff));
        }

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    public function submitAction(Element $element): ResponseInterface
    {
        if($this->updateRectorElement($element)) {
            $this->setupFlashMessage('typoscript.messages.detail.success.bodytext');
        }

        return $this->redirect('index');
    }

    public function processAllAction(): ResponseInterface
    {
        $processSuccess = $this->processStack($this->elementRepository->findByProcessed(false));

        if ($processSuccess) {
            $this->setupFlashMessage('typoscript.messages.stack.success.bodytext');
        }

        // redirect to index
        return $this->redirect('index');
    }

    public function processSelectionAction(): ResponseInterface
    {
        // SeppToDo:: $this->request->getArguments() -> add magic

        // redirect to index
        return $this->redirect('index');
    }

    public function processAction(Element $element): ResponseInterface
    {
        $processSuccess = $this->processElement($element);

        if ($processSuccess) {
            $this->setupFlashMessage('typoscript.messages.process.success.bodytext');
        }

        return $this->redirect('index');
    }

    public function applyAction(Element $element): ResponseInterface
    {
        $this->updateSysTemplateRecord($element->getOriginUid(), $element->getProcessedTyposcript());
        $element->setApplied(true);
        $this->updateRectorElement($element, 'typoscript.messages.apply.success.bodytext');

        return $this->redirect('index');
    }

    public function rollBackAction(Element $element): ResponseInterface
    {
        $this->updateSysTemplateRecord($element->getOriginUid(), $element->getOriginTyposcript());
        $element->setApplied(false);

        if($this->updateRectorElement($element)) {
            $this->setupFlashMessage('typoscript.messages.rollBack.success.bodytext');
        }

        return $this->redirect('index');
    }

    public function resetAction(Element $element): ResponseInterface
    {
        try {
            $this->elementRepository->remove($element);
            $this->elementRepository->persistAll();
            $this->setupFlashMessage('typoscript.messages.reset.success.bodytext');
        } catch (IllegalObjectTypeException) {
            $this->logger->error('The element could not be removed from the repository', ['element' => $element]);
            $this->setupFlashMessage('typoscript.messages.process.error.bodytext', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }

        return $this->redirect('index');
    }

    private function getDataEntries(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->select('uid', 'pid', 'title', 'config', 'tstamp')
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

    private function processStack(QueryResult $elements): bool
    {
        $this->stackProcess = true;

        if ($elements->count() === 0) {
            return true;
        }

        $stackResult = true;

        foreach ($elements as $element) {
            $processSuccess = $this->processElement($element);

            if ($processSuccess === false) {
                $stackResult = false;
            }
        }

        return $stackResult;
    }

    private function processElement(Element $element): bool
    {
        $elementResult = $this->rectorService->process($element->getOriginTyposcript());

        if ($elementResult === false) {
            $this->processingError = true;
            return false;
        }

        $element->setProcessedTyposcript($elementResult);
        $element->setProcessed(true);

        return $this->updateRectorElement($element);
    }

    private function updateRectorElement(Element $element): bool
    {
        try {
            $element->setTstamp(time());
            $this->elementRepository->update($element);
            $this->elementRepository->persistAll();

            return true;

        } catch (IllegalObjectTypeException|UnknownObjectException) {
            $this->logger->error('The element could not be updated by the repository', ['element' => $element]);
            $this->logErrorOccurred = true;

            return false;
        }
    }

    private function updateSysTemplateRecord(int $uid, string $typoscript): void
    {
        if ($this->dataHandler === null) {
            $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        }

        // add page to data handler data
        $dataHandlerData[self::TABLE][$uid] = [
            'config' => $typoscript,
        ];

        $this->dataHandler->start($dataHandlerData, []);
        $this->dataHandler->process_datamap();
    }

    private function createModel(array $data): void
    {
        // CHECK IF MODEL ALREADY EXISTS
        /** @var \CReifenscheid\DbRector\Domain\Model\Element $existingModel */
        $existingModel = $this->elementRepository->findByOriginUid($data['uid'])->getFirst();

        if (
            $existingModel instanceof Element && (
                $data['tstamp'] <= $existingModel->getTstamp() ||
                !$this->hasFieldChanged($data['config'], $existingModel->getOriginTyposcript())
            )
        ) {
            if ($this->hasFieldChanged($data['title'], $existingModel->getOriginTitle())) {
                $existingModel->setOriginTitle($data['title']);
                $this->updateRectorElement($existingModel);
            }

            return;
        }

        if ($existingModel instanceof Element) {
            try {
                $this->elementRepository->remove($existingModel);
                $this->elementRepository->persistAll();
            } catch (IllegalObjectTypeException) {
                $this->logger->error('The element could not be removed from the repository', ['element' => $existingModel]);
            }
        }

        if ($data['config'] !== null) {
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

    private function hasFieldChanged(string $originalTs, string $modelTs): bool
    {
        $originalTrimmed = preg_replace('/\s+/', '', $originalTs);
        $modelTrimmed = preg_replace('/\s+/', '', $modelTs);

        // 0: strings identical | 1/-1:  strings differ
        return !(strcmp($originalTrimmed, $modelTrimmed) === 0);
    }

    private function setupFlashMessage(string $messageKey, int $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK): void
    {
        $this->addFlashMessage(LocalizationUtility::translate(self::L10N . $messageKey), LocalizationUtility::translate(self::L10N . 'general.messages.header.' . $severity), $severity);
    }
}
