<?php

namespace Core\Genre\Application\Dto;

class DeleteInputDto
{
    public function __construct(
        public string $id,
    ) {
    }
}
