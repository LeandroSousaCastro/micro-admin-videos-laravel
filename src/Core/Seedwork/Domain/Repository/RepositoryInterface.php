<?php

namespace Core\Seedwork\Domain\Repository;

use Core\Seedwork\Domain\Repository\PaginationInterface;

interface RepositoryInterface
{
    public function insert(mixed $category): mixed;
    public function findById(mixed $id): mixed;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
    public function update(mixed $category): mixed;
    public function delete(string $id): bool;
}
