<?php

namespace Core\CastMember\Application\Dto;

use Core\CastMember\Domain\Enum\CastMemberType;

class CreateInputDto
{
    public function __construct(
        public string $name,
        public CastMemberType $type,
    ) {
    }
}
