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
        $genre = $this->repository->findById($input->id);
        return new GetOutputDto(
            id: $genre->id(),
            name: $genre->name,
            is_active: $genre->isActive,
            created_at: $genre->createdAt()
        );
    }
}
