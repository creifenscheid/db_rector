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
    // SeppToDo: Definevtype of origin
    protected xxx $origin = null;
    protected string $originalData = '';
    protected string $refactoredData = '';
    protected bool $applied = false;
    protected bool $processed = false;
    
    public function getOrigin(): xxx
    {
        return $this->origin;
    }
    
    public function setOrigin(xxx $origin): Element
    {
        $this->origin = $origin;
        
        return $this;
    }
    
    public function getOriginalData(): array
    {
        return unserialize($this->originalData);
    }
    
    public function setOriginalData(array $originalData): Element
    {
        $this->originalData = serialize($originalData);
        
        return $this;
    }
    
    public function getRefactoredData(): array
    {
        return unserialize($this->refactoredData);
    }
    
    public function setRefactoredData(array $refactoredData): Element
    {
        $this->refactoredData = serialize($refactoredData);
        
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
