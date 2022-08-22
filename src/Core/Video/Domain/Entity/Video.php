<?php

namespace Core\Video\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Exception\NotificationException;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;

class Video extends Entity
{
    protected array $categoriesId = [];
    protected array $genresId = [];
    protected array $castMembersId = [];

    public function __construct(
        protected Uuid|string $id = '',
        protected ?string $title = null,
        protected ?string $description = null,
        protected ?int $yearLaunched = null,
        protected ?int $duration = null,
        protected ?bool $opened = null,
        protected ?Rating $rating = null,
        protected ?Image $thumbFile = null,
        protected ?Image $thumbHalf = null,
        protected ?Image $bannerFile = null,
        protected ?Media $trailerFile = null,
        protected ?Media $videoFile = null,
        protected bool $publish = false,
        protected \DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
        $this->validation();
    }

    public function addCategoryId(array|string $categoryId)
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategoryId(string $categoryId): void
    {
        unset($this->categoriesId[array_search($categoryId, $this->categoriesId)]);
    }

    public function addGenreId(array|string $genreId)
    {
        array_push($this->genresId, $genreId);
    }

    public function removeGenreId(string $genreId): void
    {
        unset($this->genresId[array_search($genreId, $this->genresId)]);
    }

    public function addCastMemberId(array|string $castMemberId)
    {
        array_push($this->castMembersId, $castMemberId);
    }

    public function removeCastMemberId(string $castMemberId): void
    {
        unset($this->castMembersId[array_search($castMemberId, $this->castMembersId)]);
    }

    public function thumbFile(): ?Image
    {
        return $this->thumbFile;
    }

    public function thumbHalf(): ?Image
    {
        return $this->thumbHalf;
    }

    public function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    protected function validation()
    {
        if (empty($this->title)) {
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Should not be empty or null'
            ]);
        }

        if (strlen($this->title) < 3) {
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Invalid qtd'
            ]);
        }

        if (strlen($this->description) < 3) {
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Invalid qtd'
            ]);
        }

        if ($this->notification->hasErrors())
        {
            throw new NotificationException(
                $this->notification->messages('video')
            );
        }
    }
}
