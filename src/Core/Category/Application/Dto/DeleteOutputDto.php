<?php

namespace Core\Category\Application\Dto;

class DeleteOutputDto
{
    public function __construct(
        public bool $isSuccess,
    ) {
    }
}
