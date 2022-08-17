<?php

namespace Core\Category\Domain\Entity;

use Core\Seedwork\Domain\Entity\EntityBase;
use Core\Seedwork\Domain\Validation\DomainValidation;
use Core\Seedwork\Domain\ValueObject\Uuid;

class Category extends EntityBase
{
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

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
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
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
        DomainValidation::strCanNullAndMaxLength($this->description);
    }
}
