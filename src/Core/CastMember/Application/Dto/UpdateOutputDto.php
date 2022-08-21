<?php

namespace Core\CastMember\Application\Dto;

class UpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type,
        public string $created_at = ''
    ) {
    }
}
