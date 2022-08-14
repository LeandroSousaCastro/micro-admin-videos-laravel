<?php

namespace Core\Category\Application\Dto;

class UpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string|null $description = null,
        public bool $isActive = true
    ) {
    }
}
