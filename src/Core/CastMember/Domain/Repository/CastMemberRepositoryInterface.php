<?php

namespace Core\CastMember\Domain\Repository;

use Core\Seedwork\Domain\Repository\EntityRepositoryInterface;

interface CastMemberRepositoryInterface extends EntityRepositoryInterface
{
    public function getIdsListIds(array $castMembersId = []): array;
}
