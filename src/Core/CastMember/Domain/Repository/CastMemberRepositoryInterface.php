<?php

namespace Core\CastMember\Domain\Repository;

use Core\CastMember\Domain\Entity\CastMember;
use Core\Seedwork\Domain\Repository\PaginationInterface;

interface CastMemberRepositoryInterface
{
    public function insert(CastMember $genre): CastMember;
    public function findById(string $id): CastMember;
    public function getIdsListIds(array $castMembersId = []): array;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
    public function update(CastMember $genre): CastMember;
    public function delete(string $id): bool;
}
