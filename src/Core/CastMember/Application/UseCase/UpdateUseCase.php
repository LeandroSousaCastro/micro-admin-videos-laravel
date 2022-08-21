<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;

class UpdateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(UpdateInputDto $input): UpdateOutputDto
    {
        $castMember = $this->repository->findById($input->id);


        $castMember->update(
            name: $input->name,
            type: $input->type === 0 ? $castMember->type : CastMemberType::from($input->type),
        );

        $castMemberDb = $this->repository->update($castMember);

        return new UpdateOutputDto(
            id: (string) $castMemberDb->id,
            name: $castMemberDb->name,
            type: $castMemberDb->type->value,
            created_at: $castMemberDb->createdAt(),
        );
    }
}
