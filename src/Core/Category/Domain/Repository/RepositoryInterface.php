<?php

namespace Core\Category\Domain\Repository;

use Core\Category\Domain\Entity\Category;
use Core\Category\Seedwork\Repository\PaginationInterface;

interface RepositoryInterface
{
    public function insert(Category $category): Category;
    public function findById(string $id): Category;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
    public function update(Category $category): Category;
    public function delete(string $id): bool;
    public function toCategory(object $data): Category;
}
