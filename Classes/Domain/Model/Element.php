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

    protected int $originPid = 0;

    protected string $originTitle = '';

    protected string $originTyposcript = '';

    protected string $processedTyposcript = '';

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

    public function getOriginPid(): int
    {
        return $this->originPid;
    }

    public function setOriginPid(int $originPid): Element
    {
        $this->originPid = $originPid;

        return $this;
    }

    public function getOriginTitle(): string
    {
        return $this->originTitle;
    }

    public function setOriginTitle(string $originTitle): Element
    {
        $this->originTitle = $originTitle;

        return $this;
    }

    public function getOriginTyposcript(): string
    {
        return $this->originTyposcript;
    }

    public function setOriginTyposcript(string $originTyposcript): Element
    {
        $this->originTyposcript = $originTyposcript;

        return $this;
    }

    public function getProcessedTyposcript(): string
    {
        return $this->processedTyposcript;
    }

    public function setProcessedTyposcript(string $processedTyposcript): Element
    {
        $this->processedTyposcript = $processedTyposcript;

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
