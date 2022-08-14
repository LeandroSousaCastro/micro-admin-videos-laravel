<?php

namespace Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Category\Domain\Repository\RepositoryInterface;

class GetUseCase
{
    public function __construct(protected RepositoryInterface $repository)
    {
    }

    public function execute(GetInputDto $input): GetOutputDto
    {
        $category = $this->repository->findById($input->id);
        return new GetOutputDto(
            id: $category->id,
            name: $category->name,
            description: $category->description,
            is_active: $category->isActive,
            created_at: $category->createdAt()
        );
    }
}
