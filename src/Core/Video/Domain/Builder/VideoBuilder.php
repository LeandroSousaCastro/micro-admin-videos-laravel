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

    private function reset()
    {
        $this->entity = null;
    }


    public function createEntity(object $input): void
    {
        $this->entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

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

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): void
    {
        $this->entity->setVideoFile(new Media(
            filePath: $path,
            mediaStatus: $mediaStatus
        ));
    }

    public function addTrailer(string $path): void
    {
        $this->entity->setsetTrailerFileTr(new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        ));
    }

    public function addThumb(string $path): void
    {
        $this->entity->setThumbFile(new Image(
            path: $path,
        ));
    }

    public function addThumbHalf(string $path): void
    {
        $this->entity->setThumbHalf(new Image(
            path: $path,
        ));
    }

    public function addBanner(string $path): void
    {
        $this->entity->setBannerFile(new Image(
            path: $path,
        ));
    }

    public function getEntity(): Video
    {
        return $this->entity;
    }
}
