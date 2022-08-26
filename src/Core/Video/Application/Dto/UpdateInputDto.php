<?php

namespace Core\Video\Application\Dto;

use Core\Video\Domain\Enum\Rating;

class UpdateInputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public array $categories,
        public array $genres,
        public array $castMembers,
        public ?array $thumbFile = null,
        public ?array $thumbHalf = null,
        public ?array $bannerFile = null,
        public ?array $trailerFile = null,
        public ?array $videoFile = null,
    ) {
    }
}
