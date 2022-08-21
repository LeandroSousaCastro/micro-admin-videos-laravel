<?php

namespace Core\CastMember\Application\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;

class CreateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
    }

    public function execute(CreateInputDto $input): CreateOutputDto
    {
        $entity = new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type)
        );

        $entityDb = $this->repository->insert($entity);

        return new CreateOutputDto(
            id: $entityDb->id,
            name: $entityDb->name,
            type: $entityDb->type->value,
            created_at: $entityDb->createdAt()
        );
    }
}
