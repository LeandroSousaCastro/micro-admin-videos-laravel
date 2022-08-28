<?php

namespace Core\CastMember\Domain\Entity;

use Core\CastMember\Domain\Enum\CastMemberType;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ValidationTrait;
use Core\Seedwork\Domain\ValueObject\Uuid;
use DateTime;

class CastMember extends Entity
{
    use ValidationTrait;

    protected $rules = [
        'name' => 'required|min:3|max:255',
    ];

    public function __construct(
        protected string $name,
        protected CastMemberType $type,
        protected ?Uuid $id = null,
        protected ?\DateTime $createdAt = null
    ) {
        parent::__construct($id, $createdAt);
        $this->validate();
    }

    public function update(
        string $name,
        ?CastMemberType $type = null
    ): void {
        $this->name = $name;
        if ($type) {
            $this->type = $type;
        }
        $this->validate();
    }
}
