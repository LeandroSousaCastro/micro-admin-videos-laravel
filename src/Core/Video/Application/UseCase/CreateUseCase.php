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
use Core\Video\Domain\Events\VideoCreated;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Dotenv\Parser\Entry;
use Throwable;

class CreateUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected GenreRepositoryInterface $genreRepository,
        protected CastMemberRepositoryInterface $castMembersResponse,
        protected DbTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager
    ) {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        $entity = $this->createEntity($input);

        try {
            $this->repository->insert($entity);
            $pathMedia = $this->storeMedia(
                $entity->id,
                $input->videoFile
            );
            if ($pathMedia) {
                $this->eventManager->dispatch(
                    new VideoCreated($entity)
                );
            }
            $this->transaction->commit();
        } catch (Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }

        return $this->output($entity);
    }

    private function createEntity(CreateInputDto $input): Entity
    {
        $entity = new EntityVideo(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

        $this->validateCategoriesId($input->categories);
        foreach ($input->categories as $categoryId) {
            $entity->addCategoryId($categoryId);
        }

        $this->validateGenresId($input->genres);
        foreach ($input->genres as $genreId) {
            $entity->addGenreId($genreId);
        }

        $this->validateCastMembersId($input->castMembers);
        foreach ($input->castMembers as $castMemberId) {
            $entity->addCastMemberId($castMemberId);
        }

        return $entity;
    }

    private function storeMedia(string $path, ?array $media = null): ?string
    {
        if ($media) {
            return $this->storage->store(
                path: $path,
                file: $media
            );
        }

        return null;
    }

    private function validateCategoriesId(array $categoriesId = []): void
    {
        $categories = $this->categoryRepository->getIdsListIds($categoriesId);
        $arrayDiff = array_diff($categoriesId, $categories);
        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'Categories' : 'Category',
                implode(', ', $arrayDiff)
            );

            throw new NotFoundException($msg);
        }
    }

    private function validateGenresId(array $genresId = []): void
    {
        $genres = $this->categoryRepository->getIdsListIds($genresId);
        $arrayDiff = array_diff($genresId, $genres);
        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'Genres' : 'Genre',
                implode(', ', $arrayDiff)
            );

            throw new NotFoundException($msg);
        }
    }

    private function validateCastMembersId(array $castMembersId = []): void
    {
        $castMembers = $this->categoryRepository->getIdsListIds($castMembersId);
        $arrayDiff = array_diff($castMembersId, $castMembers);
        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'CastMembers' : 'CastMember',
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
