<?php

namespace Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Category\Domain\Repository\CategoryRepositoryInterface;

class UpdateUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
    }

    public function execute(UpdateInputDto $input): UpdateOutputDto
    {
        $category = $this->repository->findById($input->id);

        $category->update(
            name: $input->name,
            description: $input->description ?? $category->description,
        );

        $categoryUpdated = $this->repository->update($category);

        return new UpdateOutputDto(
            id: $categoryUpdated->id,
            name: $categoryUpdated->name,
            description: $categoryUpdated->description,
            is_active: $categoryUpdated->isActive,
            created_at: $categoryUpdated->createdAt()
        );
    }
}
