<?php

namespace Core\CastMember\Application\Dto;

use Core\CastMember\Domain\Enum\CastMemberType;

class GetOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public CastMemberType $type,
        public string $created_at = ''
    ) {
    }
}
