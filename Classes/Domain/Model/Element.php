<?php

namespace CReifenscheid\DbRector\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
 * Class Element
 */
class Element extends AbstractEntity
{
    protected int $originUid = 0;

    protected string $originInformation = '';

    protected string $originTable = '';

    protected string $originData = '';

    protected string $processedData = '';

    protected bool $applied = false;

    protected bool $processed = false;

    public function getOriginUid(): int
    {
        return $this->originUid;
    }

    public function setOriginUid(int $originUid): Element
    {
        $this->originUid = $originUid;

        return $this;
    }

    public function getOriginInformation(): array
    {
        return unserialize($this->originInformation, ['allowed_classes' => false]);
    }

    public function setOriginInformation(array $originInformation): Element
    {
        $this->originInformation = serialize($originInformation);

        return $this;
    }

    public function getOriginTable(): string
    {
        return $this->originTable;
    }

    public function setOriginTable(string $originTable): Element
    {
        $this->originTable = $originTable;

        return $this;
    }

    public function getOriginData(): string
    {
        return $this->originData;
    }

    public function setOriginData(string $originData): Element
    {
        $this->originData = $originData;

        return $this;
    }

    public function getProcessedData(): array
    {
        if ($this->processedData === '') {
            return [];
        }

        return unserialize($this->processedData, ['allowed_classes' => false]);
    }

    public function setProcessedData(array $processedData): Element
    {
        $this->processedData = serialize($processedData);

        return $this;
    }

    public function getApplied(): bool
    {
        return $this->applied;
    }

    public function setApplied(bool $applied): Element
    {
        $this->applied = $applied;

        return $this;
    }

    public function getProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): Element
    {
        $this->processed = $processed;

        return $this;
    }
}
