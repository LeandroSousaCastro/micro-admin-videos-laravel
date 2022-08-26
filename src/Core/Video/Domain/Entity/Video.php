<?php

namespace Core\Video\Domain\Entity;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Entity\Traits\ValidationTrait;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;

class Video extends Entity
{
    use ValidationTrait;

    protected array $categoriesId = [];
    protected array $genresId = [];
    protected array $castMembersId = [];
    protected array $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'required|min:3|max:255',
        'yearLaunched' => 'required|integer',
        'duration' => 'required|integer',
    ];


    public function __construct(
        protected string $title,
        protected string $description,
        protected int $yearLaunched,
        protected int $duration,
        protected bool $opened,
        protected Rating $rating,
        protected ?Image $thumbFile = null,
        protected ?Image $thumbHalf = null,
        protected ?Image $bannerFile = null,
        protected ?Media $trailerFile = null,
        protected ?Media $videoFile = null,
        protected bool $publish = false,
        protected Uuid|string $id = '',
        protected \DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
        $this->validate();
    }

    public function update(string $title, string $description): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->validate();
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

    public function setThumbFile(Image $thumbFile): void
    {
        $this->thumbFile = $thumbFile;
    }

    public function thumbHalf(): ?Image
    {
        return $this->thumbHalf;
    }

    public function setThumbHalf(Image $thumbHalf): void
    {
        $this->thumbHalf = $thumbHalf;
    }

    public function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public function setBannerFile(Image $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }

    public function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public function setTrailerFile(Media $trailerFile): void
    {
        $this->trailerFile = $trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    public function setVideoFile(Media $videoFile): void
    {
        $this->videoFile = $videoFile;
    }
}
