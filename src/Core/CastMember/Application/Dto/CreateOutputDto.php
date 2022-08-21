<?php

namespace Core\CastMember\Application\Dto;

class CreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type,
        public string $created_at = ''
    ) {
    }
}
