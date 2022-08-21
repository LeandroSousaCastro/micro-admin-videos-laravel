<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;

class UpdateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(UpdateInputDto $input): UpdateOutputDto
    {
        $castMember = $this->repository->findById($input->id);

        try {
            $castMember->update(
                name: $input->name,
                type: $input->type,
            );

            $castMemberDb = $this->repository->update($castMember);

            return new UpdateOutputDto(
                id: (string) $castMemberDb->id,
                name: $castMemberDb->name,
                is_active: $castMemberDb->isActive,
                created_at: $castMemberDb->createdAt(),
            );
        } catch (\Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }
}
