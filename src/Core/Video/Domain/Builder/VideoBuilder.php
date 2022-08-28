<?php

namespace Core\Video\Domain\Builder;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;

class VideoBuilder implements BuilderInterface
{

    protected ?Video $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    private function reset(): void
    {
        $this->entity = null;
    }


    public function createEntity(object $input): BuilderInterface
    {
        $this->entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

        $this->addIds($input);

        return $this;
    }

    public function addIds(object $input): void
    {
        foreach ($input->categories as $categoryId) {
            $this->entity->addCategoryId($categoryId);
        }

        foreach ($input->genres as $genreId) {
            $this->entity->addGenreId($genreId);
        }

        foreach ($input->castMembers as $castMemberId) {
            $this->entity->addCastMemberId($castMemberId);
        }
    }

    public function addMediaVideo(string $path, MediaStatus $mediaStatus, string $encodedPath = ''): BuilderInterface
    {
        $this->entity->setVideoFile(new Media(
            filePath: $path,
            mediaStatus: $mediaStatus,
            encodedPath: $encodedPath
        ));

        return $this;
    }

    public function addTrailer(string $path): BuilderInterface
    {
        $this->entity->setTrailerFile(new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        ));

        return $this;
    }

    public function addThumb(string $path): BuilderInterface
    {
        $this->entity->setThumbFile(new Image(
            path: $path,
        ));

        return $this;
    }

    public function addThumbHalf(string $path): BuilderInterface
    {
        $this->entity->setThumbHalf(new Image(
            path: $path,
        ));

        return $this;
    }

    public function addBanner(string $path): BuilderInterface
    {
        $this->entity->setBannerFile(new Image(
            path: $path,
        ));

        return $this;
    }

    public function getEntity(): Video
    {
        return $this->entity;
    }
}
