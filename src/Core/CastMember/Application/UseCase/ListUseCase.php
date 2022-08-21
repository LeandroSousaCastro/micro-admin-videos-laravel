<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    ListInputDto, ListOutputDto
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;

class ListUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(ListInputDto $inputDto): ListOutputDto
    {
        $response = $this->repository->paginate(
            $inputDto->filter,
            $inputDto->order,
            $inputDto->page,
            $inputDto->totalPage
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
