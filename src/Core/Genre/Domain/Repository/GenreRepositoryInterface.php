<?php

namespace Core\Genre\Domain\Repository;

use Core\Seedwork\Domain\Repository\EntityRepositoryInterface;

interface GenreRepositoryInterface extends EntityRepositoryInterface
{
    public function getIdsListIds(array $genresId = []): array;
}
