<?php

namespace Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Category\Domain\Repository\RepositoryInterface;

class ListUseCase
{
    public function __construct(protected RepositoryInterface $repository)
    {
    }

    public function execute(ListInputDto $input): ListOutputDto
    {
        $categories = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );

        return new ListOutputDto(
            items: $categories->items(),
            total: $categories->total(),
            current_page: $categories->currentPage(),
            last_page: $categories->lastPage(),
            first_page: $categories->firstPage(),
            per_page: $categories->perPage(),
            to: $categories->to(),
            from: $categories->from()
        );
    }
}
