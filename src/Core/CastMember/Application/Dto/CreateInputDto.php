<?php

namespace Core\CastMember\Application\Dto;

class CreateInputDto
{
    public function __construct(
        public string $name,
        public int $type,
    ) {
    }
}
