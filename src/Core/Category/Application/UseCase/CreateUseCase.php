<?php

namespace Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\RepositoryInterface;

class CreateUseCase
{
    public function __construct(protected RepositoryInterface $repository)
    {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        $category = new Category(
            name: $input->name,
            description: $input->description,
            isActive: $input->isActive
        );

        $newCategory = $this->repository->insert($category);

        return new CreateOutputDto(
            id: $newCategory->id,
            name: $newCategory->name,
            description: $newCategory->description,
            is_active: $newCategory->isActive,
            created_at: $newCategory->createdAt()
        );
    }
}
