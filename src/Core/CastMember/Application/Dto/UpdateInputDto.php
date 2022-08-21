<?php

namespace Core\CastMember\Application\Dto;

class UpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type
    ) {
    }
}
