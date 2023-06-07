<?php

namespace CReifenscheid\DbRector\Domain\Model;

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
    protected string $origin = '';
    protected string $originTable = '';
    protected string $originData = '';
    protected string $processedData = '';
    protected bool $applied = false;
    protected bool $processed = false;

    public function getOrigin(): array
    {
        return unserialize($this->origin, ['allowed_classes' => false]);
    }

    public function setOrigin(array $origin): Element
    {
        $this->origin = serialize($origin);

        return $this;
    }

    public function getOriginTable(): string
    {
        return $this->originTable;
    }

    public function setOriginTable(string $originTable): void
    {
        $this->originTable = $originTable;
    }

    public function getOriginData(): array
    {
        return unserialize($this->originData, ['allowed_classes' => false]);
    }

    public function setOriginData(array $originData): Element
    {
        $this->originData = serialize($originData);

        return $this;
    }

    public function getProcessedData(): array
    {
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
