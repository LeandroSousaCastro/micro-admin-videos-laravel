<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto,
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;

class DeleteUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(DeleteInputDto $input): DeleteOutputDto
    {
        $result = $this->repository->delete($input->id);
        return new DeleteOutputDto(isSuccess: $result);
    }
}
