<?php

namespace Core\Genre\Application\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;

class UpdateUseCase
{
    protected $repository;
    protected $transaction;
    protected $categoryRepository;

    public function __construct(
        GenreRepositoryInterface $repository,
        CategoryRepositoryInterface $categoryRepository,
        DbTransactionInterface $transaction,
    ) {
        $this->repository = $repository;
        $this->transaction = $transaction;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(UpdateInputDto $input): UpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);

        try {
            $genre->update(
                name: $input->name,
            );
            foreach ($input->categoriesId as $categoryId) {
                $genre->addCategory($categoryId);
            }

            $this->validateCategoriesId($input->categoriesId);

            $genreDb = $this->repository->update($genre);

            $this->transaction->commit();

            return new UpdateOutputDto(
                id: (string) $genreDb->id,
                name: $genreDb->name,
                is_active: $genreDb->isActive,
                created_at: $genreDb->createdAt(),
            );
        } catch (\Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }

    public function validateCategoriesId(array $categoriesId = [])
    {
        $categoriesDb = $this->categoryRepository->getIdsListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);

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