<?php

namespace Core\Category\Application\Dto;

class UpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public bool $is_active,
        public string $created_at = ''
    ) {
    }
}
