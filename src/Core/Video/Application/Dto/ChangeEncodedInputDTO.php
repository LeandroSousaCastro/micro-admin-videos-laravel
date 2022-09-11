<?php

namespace Core\Video\Application\Dto;

class ChangeEncodedInputDTO
{
    public function __construct(
        public string $id,
        public string $encodedPath,
    ) {
    }
}
