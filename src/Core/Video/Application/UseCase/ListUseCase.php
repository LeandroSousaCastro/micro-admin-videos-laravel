<?php

namespace Core\Video\Application\UseCase;

use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Video\Application\Dto\{
    ListInputDto
};
use Core\Video\Domain\Repository\VideoRepositoryInterface;

class ListUseCase
{
    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(ListInputDto $input): PaginationInterface
    {
        return $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
    }
}
