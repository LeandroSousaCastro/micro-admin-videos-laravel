<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto,
};
use Core\Video\Domain\Repository\VideoRepositoryInterface;

class DeleteUseCase
{
    public function __construct(protected VideoRepositoryInterface $repository)
    {
    }

    public function execute(DeleteInputDto $input): DeleteOutputDto
    {
        $result = $this->repository->delete($input->id);
        return new DeleteOutputDto(isSuccess: $result);
    }
}
