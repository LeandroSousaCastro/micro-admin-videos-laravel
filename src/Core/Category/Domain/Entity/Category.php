<?php

namespace Core\Category\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ActivateDeactivateTrait;
use Core\Seedwork\Domain\Entity\Traits\ValidationTrait;
use Core\Seedwork\Domain\ValueObject\Uuid;

class Category extends Entity
{
    use ActivateDeactivateTrait, ValidationTrait;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'nullable|min:3|max:255',
    ];

    public function __construct(
        protected string $name,
        protected string $description = '',
        protected bool $isActive = true,
        protected ?Uuid $id = null,
        protected ?\DateTime $createdAt = null
    ) {
        parent::__construct($id, $createdAt);
        $this->validate();
    }

    public function update(
        string $name,
        string $description = '',
    ): void {
        $this->name = $name;
        if ($description != '') {
            $this->description = $description;
        }
        $this->validate();
    }
}
