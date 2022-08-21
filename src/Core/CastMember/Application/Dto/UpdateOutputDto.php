<?php

namespace Core\CastMember\Application\Dto;

class UpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active,
        public string $created_at = ''
    ) {
    }
}
