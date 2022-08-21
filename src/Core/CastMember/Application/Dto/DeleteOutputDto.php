<?php

namespace Core\CastMember\Application\Dto;

class DeleteOutputDto
{
    public function __construct(
        public bool $isSuccess,
    ) {
    }
}
