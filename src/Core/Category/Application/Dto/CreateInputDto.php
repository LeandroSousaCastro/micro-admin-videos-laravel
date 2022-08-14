<?php

namespace Core\Category\Application\Dto;

class CreateInputDto
{
    public function __construct(
        public string $name,
        public string $description = '',
        public bool $isActive = true
    ) {
    }
}
