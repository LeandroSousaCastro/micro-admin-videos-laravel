<?php

namespace Core\Category\Domain\Repository;

use Core\Seedwork\Domain\Repository\EntityRepositoryInterface;

interface CategoryRepositoryInterface extends EntityRepositoryInterface
{
    public function getIdsListIds(array $categoriesId = []): array;
}
