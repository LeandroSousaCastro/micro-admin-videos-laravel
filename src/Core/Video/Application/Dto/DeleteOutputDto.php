<?php

namespace Core\Video\Application\Dto;

class DeleteOutputDto
{
    public function __construct(
        public bool $isSuccess,
    ) {
    }
}
