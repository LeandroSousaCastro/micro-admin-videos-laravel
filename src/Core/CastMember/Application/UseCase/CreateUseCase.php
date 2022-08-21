<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\CastMember\Domain\Entity\CastMember;

class CreateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository) {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        try {
           $castMember = new CastMember(
                name: $input->name,
                type: $input->type
            );

            return new CreateOutputDto(
                id: $castMember->id,
                name: $castMember->name,
                type: $castMember->type,
                created_at: $castMember->createdAt()
            );
        } catch (\Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }
    }
}
