<?php

namespace Core\Video\Application\Dto;

use Core\Video\Domain\Enum\Rating;

class CreateOutputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public bool $opened,
        public Rating $rating,
        public array $categories = [],
        public array $genres = [],
        public array $castMembers = [],
        public ?string $thumbFile = null,
        public ?string $thumbHalf = null,
        public ?string $bannerFile = null,
        public ?string $trailerFile = null,
        public ?string $videoFile = null,
    ) {
    }
}
