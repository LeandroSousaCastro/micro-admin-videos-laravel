<?php

namespace Core\Genre\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ActivateDeactivateTrait;
use Core\Seedwork\Domain\Validation\DomainValidation;
use Core\Seedwork\Domain\ValueObject\Uuid;
use DateTime;

class Genre extends Entity
{
    use ActivateDeactivateTrait;

    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected array $categoriesId = [],
        protected bool $isActive = false,
        protected DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
        $this->validate();
    }

    public function update(
        string $name = ''
    ): void {
        $this->name = $name;
        $this->validate();
    }

    public function addCategory(string $categoryId): void
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategory(string $categoryId): void
    {
        unset($this->categoriesId[array_search($categoryId, $this->categoriesId)]);
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
    }
}
