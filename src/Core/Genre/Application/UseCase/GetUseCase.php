<?php

namespace Core\Genre\Application\UseCase;

use Core\Genre\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Genre\Domain\Repository\GenreRepositoryInterface;

class GetUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
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
