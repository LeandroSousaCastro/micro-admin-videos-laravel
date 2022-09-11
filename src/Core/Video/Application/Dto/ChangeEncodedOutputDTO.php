<?php

namespace Core\Video\Application\Dto;

class ChangeEncodedOutputDTO
{
    public function __construct(
        public string $id,
        public string $encodedPath,
    ) {
    }
}
