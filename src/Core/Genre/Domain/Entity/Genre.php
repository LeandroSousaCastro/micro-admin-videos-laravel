<?php

namespace Core\Genre\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ActivateDeactivateTrait;
use Core\Seedwork\Domain\Entity\Traits\ValidationTrait;
use Core\Seedwork\Domain\ValueObject\Uuid;
use DateTime;

class Genre extends Entity
{
    use ActivateDeactivateTrait, ValidationTrait;

    protected $rules = [
        'name' => 'required|min:3|max:255',
    ];

    public function __construct(
        protected string $name,
        protected array $categoriesId = [],
        protected bool $isActive = false,
        protected ?Uuid $id = null,
        protected ?\DateTime $createdAt = null
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

    public function addCategory(array|string $categoryId): void
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategory(string $categoryId): void
    {
        unset($this->categoriesId[array_search($categoryId, $this->categoriesId)]);
    }
}
