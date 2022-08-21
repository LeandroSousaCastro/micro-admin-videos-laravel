<?php

namespace Core\CastMember\Domain\Entity;

use Core\CastMember\Domain\Enum\CastMemberType;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Validation\DomainValidation;
use Core\Seedwork\Domain\ValueObject\Uuid;
use DateTime;

class CastMember extends Entity
{
    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected CastMemberType|int|null $type = null,
        protected DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
        $this->type = is_int($type) ? CastMemberType::from($type) : $type;
        $this->validate();
    }

    public function update(
        string $name,
        CastMemberType|int|null $type = null
    ): void {
        $this->name = $name;
        if ($type) {
            $this->type = $type;
        }
        $this->validate();
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
        DomainValidation::notNull($this->type);
    }
}
