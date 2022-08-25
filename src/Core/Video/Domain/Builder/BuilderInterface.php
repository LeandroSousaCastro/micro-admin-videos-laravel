<?php

namespace Core\Video\Domain\Builder;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\MediaStatus;

interface BuilderInterface
{
    public function createEntity(object $input): BuilderInterface;
    public function addMediaVideo(string $path, MediaStatus $mediaStatus): BuilderInterface;
    public function addTrailer(string $path): BuilderInterface;
    public function addThumb(string $path): BuilderInterface;
    public function addThumbHalf(string $path): BuilderInterface;
    public function addBanner(string $path): BuilderInterface;
    public function getEntity(): Video;
}
