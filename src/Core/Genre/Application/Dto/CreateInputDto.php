<?php

namespace Core\Genre\Application\Dto;

class CreateInputDto
{
    public function __construct(
        public string $name,
        public array $categoriesId = [],
        public bool $isActive = true
    ) {
    }
}
