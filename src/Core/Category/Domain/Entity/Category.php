<?php

namespace Core\Category\Domain\Entity;

use Core\Category\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Seedwork\Domain\Validation\DomainValidation;
use Core\Seedwork\Domain\ValueObject\Uuid;

class Category
{
    use MethodsMagicsTrait;

    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected string $description = '',
        protected bool $isActive = true,
        protected \DateTime|string $createdAt = ''
    ) {
        $this->id = $this->id ? new Uuid($this->id) : Uuid::random();
        $this->createdAt = $this->createdAt ? new \DateTime($this->createdAt) : new \DateTime();
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
