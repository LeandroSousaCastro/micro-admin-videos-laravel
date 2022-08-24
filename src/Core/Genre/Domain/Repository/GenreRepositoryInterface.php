<?php

namespace Core\Genre\Domain\Repository;

use Core\Genre\Domain\Entity\Genre;
use Core\Seedwork\Domain\Repository\PaginationInterface;

interface GenreRepositoryInterface
{
    public function insert(Genre $genre): Genre;
    public function findById(string $id): Genre;
    public function getIdsListIds(array $genresId = []): array;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
    public function update(Genre $genre): Genre;
    public function delete(string $id): bool;
}
