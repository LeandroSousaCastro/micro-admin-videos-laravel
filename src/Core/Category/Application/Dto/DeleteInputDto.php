<?php

namespace Core\Category\Application\Dto;

class DeleteInputDto
{
    public function __construct(
        public string $id,
    ) {
    }
}
