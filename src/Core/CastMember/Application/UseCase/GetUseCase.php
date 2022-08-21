<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;

class GetUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(GetInputDto $input): GetOutputDto
    {
        $genre = $this->repository->findById($input->id);
        return new GetOutputDto(
            id: $genre->id,
            name: $genre->name,
            type: $genre->type->value,
            created_at: $genre->createdAt()
        );
    }
}
