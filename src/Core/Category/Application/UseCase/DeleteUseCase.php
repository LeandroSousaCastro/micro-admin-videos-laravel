<?php

namespace Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto,
};
use Core\Category\Domain\Repository\CategoryRepositoryInterface;

class DeleteUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
    }

    public function execute(DeleteInputDto $input): DeleteOutputDto
    {
        $result = $this->repository->delete($input->id);
        return new DeleteOutputDto(isSuccess: $result);
    }
}
