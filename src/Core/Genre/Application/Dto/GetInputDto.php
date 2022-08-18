<?php

namespace Core\Genre\Application\Dto;

class GetInputDto
{
    public function __construct(
        public string $id,
    ) {
    }
}
