<?php

namespace Core\Category\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ActivateDeactivateTrait;
use Core\Seedwork\Domain\Validation\DomainValidation;
use Core\Seedwork\Domain\ValueObject\Uuid;

class Category extends Entity
{
    use ActivateDeactivateTrait;
    
    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected string $description = '',
        protected bool $isActive = true,
        protected \DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
        $this->validate();
    }

    public function update(
        string $name,
        string $description = '',
    ): void {
        $this->validate();
        $this->name = $name;
        $this->description = $description;
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
        DomainValidation::strCanNullAndMaxLength($this->description);
    }
}
