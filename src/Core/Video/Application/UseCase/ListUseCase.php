<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Video\Domain\Repository\VideoRepositoryInterface;

class ListUseCase
{
    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(ListInputDto $input): ListOutputDto
    {
        $response = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );

        return new ListOutputDto(
            items: $response->items(),
            total: $response->total(),
            current_page: $response->currentPage(),
            last_page: $response->lastPage(),
            first_page: $response->firstPage(),
            per_page: $response->perPage(),
            to: $response->to(),
            from: $response->from()
        );
    }
}
