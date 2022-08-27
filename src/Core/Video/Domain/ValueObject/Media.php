<?php

namespace Core\Video\Domain\ValueObject;

use Core\Seedwork\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Video\Domain\Enum\MediaStatus;

class Media
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $filePath,
        protected MediaStatus $mediaStatus,
        protected string $encodedPath = '',
    ) {
    }
}
