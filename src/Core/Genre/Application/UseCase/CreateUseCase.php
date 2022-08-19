<?php

namespace Core\Genre\Application\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Genre\Domain\Entity\Genre;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;

class CreateUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected DbTransactionInterface $transaction
    ) {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        try {
            $category = new Genre(
                name: $input->name,
                categoriesId: $input->categoriesId,
                isActive: $input->isActive
            );

            $this->validateCategoriesId($input->categoriesId);

            $newGenre = $this->repository->insert($category);

            $this->transaction->commit();

            return new CreateOutputDto(
                id: $newGenre->id,
                name: $newGenre->name,
                is_active: $newGenre->isActive,
                created_at: $newGenre->createdAt()
            );
        } catch (\Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }
    }

    public function validateCategoriesId(array $categoriesId = []): void
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
}
