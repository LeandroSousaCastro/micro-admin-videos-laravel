<?php

namespace Core\Genre\Application\Dto;

class DeleteOutputDto
{
    public function __construct(
        public bool $isSuccess,
    ) {
    }
}
