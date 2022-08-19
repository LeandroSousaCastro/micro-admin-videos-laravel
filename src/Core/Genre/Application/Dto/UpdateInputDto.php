<?php

namespace Core\Genre\Application\Dto;

class UpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public array $categoriesId = [],
        public bool $isActive = true
    ) {
    }
}
