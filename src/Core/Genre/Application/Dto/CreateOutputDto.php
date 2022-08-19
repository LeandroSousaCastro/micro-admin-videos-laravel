<?php

namespace Core\Genre\Application\Dto;

class CreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active,
        public string $created_at = ''
    ) {
    }
}
