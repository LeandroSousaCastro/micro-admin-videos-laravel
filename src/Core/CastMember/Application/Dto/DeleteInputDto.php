<?php

namespace Core\CastMember\Application\Dto;

class DeleteInputDto
{
    public function __construct(
        public string $id,
    ) {
    }
}
