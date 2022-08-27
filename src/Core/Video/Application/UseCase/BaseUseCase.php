<?php

namespace Core\Video\Application\UseCase;

use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\{
    DbTransactionInterface,
    FileStorageInterface
};
use Core\Video\Domain\Builder\BuilderInterface;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Events\VideoCreatedEvent;

abstract class BaseUseCase
{
    protected BuilderInterface $builder;

    abstract protected function getBuilder(): BuilderInterface;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected GenreRepositoryInterface $genreRepository,
        protected CastMemberRepositoryInterface $castMembersRepository,
        protected DbTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager
    ) {
        $this->builder = $this->getBuilder();
    }

    protected function storageFiles(object $input): void
    {
        $entity = $this->builder->getEntity();
        $path = $entity->id;
        if ($pathVideoFile = $this->storageFile($path, $input->videoFile)) {
            $this->builder->addMediaVideo($pathVideoFile, MediaStatus::PROCESSING);
            $this->eventManager->dispatch(new VideoCreatedEvent($entity));
        }

        if ($pathTrailerFile = $this->storageFile($path, $input->trailerFile)) {
            $this->builder->addTrailer($pathTrailerFile);
        }

        if ($pathThumbFile = $this->storageFile($path, $input->thumbFile)) {
            $this->builder->addThumb($pathThumbFile);
        }

        if ($pathThumbHalf = $this->storageFile($path, $input->thumbHalf)) {
            $this->builder->addThumbHalf($pathThumbHalf);
        }

        if ($pathBannerFile = $this->storageFile($path, $input->bannerFile)) {
            $this->builder->addBanner($pathBannerFile);
        }
    }

    protected function storageFile(string $path, ?array $media = null): ?string
    {
        if ($media) {
            return $this->storage->store(
                path: $path,
                file: $media
            );
        }

        return null;
    }

    protected function validateAllIds(object $input)
    {
        $this->validateIds($input->categories, $this->categoryRepository, "Category", "Categories");
        $this->validateIds($input->genres, $this->genreRepository, "Genre");
        $this->validateIds($input->castMembers, $this->castMembersRepository, "CastMembers");
    }

    protected function validateIds(array $ids, $repository, string $singularLabel, ?string $pluralLabel = null): void
    {
        $idsDb = $repository->getIdsListIds($ids);
        $arrayDiff = array_diff($ids, $idsDb);
        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? $pluralLabel ?? $singularLabel . 's'  : $singularLabel,
                implode(', ', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }
}
