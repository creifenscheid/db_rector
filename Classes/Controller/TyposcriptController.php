<?php

namespace CReifenscheid\DbRector\Controller;

use CReifenscheid\DbRector\Domain\Model\Element;
use CReifenscheid\DbRector\Domain\Repository\ElementRepository;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

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
    protected array $propertiesToRefactor = [
        'constants',
        'config',
    ];

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

        $this->view->assign('elements', $this->elementRepository->findByOriginTable(self::TABLE));

        // show models in view
        // implement toolbar
        // run rector on entry
        // apply rector result to original entry
    }

    private function getDataEntries(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->select('uid', 'pid', 'title', 'constants', 'config')
        ->from(self::TABLE)
        ->where(
            $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0)),
        );

        try {
            return $queryBuilder->executeQuery()->fetchAllAssociative();
        } catch (Exception $e) {
        }

        return [];
    }

    private function createModel(array $data): void
    {
        // CHECK IF MODEL ALREADY EXISTS
        $existingModel = $this->elementRepository->findByUidAndTable($data['uid'], self::TABLE);
        if (($existingModel instanceof QueryResult && $existingModel->count() > 0) || (is_array($existingModel) && count($existingModel) > 0)) {
            return;
        }

        // PREPARE DATA TO REFACTOR
        $dataToProcess = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->propertiesToRefactor, true)) {
                $dataToProcess[$key] = $value;
            }
        }

        // SET UP MODEL
        $element = new Element();
        $element
            ->setOriginUid($data['uid'])
            ->setOriginTable(self::TABLE)
            ->setOriginData($dataToProcess);

        try {
            $this->elementRepository->add($element);
        } catch (IllegalObjectTypeException $e) {
        }
    }
}
