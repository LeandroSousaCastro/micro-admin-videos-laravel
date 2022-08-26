<?php

namespace Core\Video\Application\UseCase;

use Core\Genre\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto,
};
use Core\Genre\Domain\Repository\GenreRepositoryInterface;

class DeleteUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
    {
    }

    public function execute(DeleteInputDto $input): DeleteOutputDto
    {
        $result = $this->repository->delete($input->id);
        return new DeleteOutputDto(isSuccess: $result);
    }
}
