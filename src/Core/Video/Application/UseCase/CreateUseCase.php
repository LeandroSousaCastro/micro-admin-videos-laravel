<?php

namespace Core\Video\Application\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\{
    DbTransactionInterface,
    FileStorageInterface
};
use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Video\Domain\Entity\Video as EntityVideo;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Events\VideoCreated;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Throwable;

class CreateUseCase
{
    protected EntityVideo $entity;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected GenreRepositoryInterface $genreRepository,
        protected CastMemberRepositoryInterface $castMembersRepository,
        protected DbTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager
    ) {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        $this->validateAllIds($input);
        $this->entity = $this->createEntity($input);

        try {
            $this->repository->insert($this->entity);
            $this->storageFiles($input);
            $this->repository->updateMedia($this->entity);
            $this->transaction->commit();
        } catch (Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }

        return $this->output($this->entity);
    }

    private function createEntity(CreateInputDto $input): Entity
    {
        $this->entity = new EntityVideo(
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

        return $this->entity;
    }

    protected function storageFiles(object $input): void
    {
        if ($pathVideoFile = $this->storageFile($this->entity->id, $input->videoFile)) {
            $this->entity->setVideoFile(new Media(
                filePath: $pathVideoFile,
                mediaStatus: MediaStatus::PROCESSING
            ));
            $this->eventManager->dispatch(new VideoCreated($this->entity));
        }

        if ($pathTrailerFile = $this->storageFile($this->entity->id, $input->trailerFile)) {
            $this->entity->setTrailerFile(new Media(
                filePath: $pathTrailerFile,
                mediaStatus: MediaStatus::PROCESSING
            ));
        }

        if ($pathThumbFile = $this->storageFile($this->entity->id, $input->thumbFile)) {
            $this->entity->setThumbFile(new Image(
                path: $pathThumbFile,
            ));
        }

        if ($pathThumbHalf = $this->storageFile($this->entity->id, $input->thumbHalf)) {
            $this->entity->setThumbHalf(new Image(
                path: $pathThumbHalf,
            ));
        }

        if ($pathBannerFile = $this->storageFile($this->entity->id, $input->bannerFile)) {
            $this->entity->setBannerFile(new Image(
                path: $pathBannerFile,
            ));
        }
    }

    private function storageFile(string $path, ?array $media = null): ?string
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
                '%s %x not found',
                count($arrayDiff) > 1 ? $pluralLabel ?? $singularLabel . 's'  : $singularLabel,
                implode(', ', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    private function output(EntityVideo $entity): CreateOutputDto
    {
        return new CreateOutputDto(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            categories: $entity->categoriesId,
            genres: $entity->genresId,
            castMembers: $entity->castMembersId,
            thumbFile: $entity->thumbFile()?->filePath,
            thumbHalf: $entity->thumbHalf()?->filePath,
            bannerFile: $entity->bannerFile()?->filePath,
            trailerFile: $entity->trailerFile()?->filePath,
            videoFile: $entity->videoFile()?->filePath,
        );
    }
}
