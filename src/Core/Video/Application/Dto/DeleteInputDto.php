<?php

namespace Core\Video\Application\Dto;

class DeleteInputDto
{
    public function __construct(
        public string $id,
    ) {
    }
}
