<?php

namespace Core\Video\Domain\Repository;

use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Repository\EntityRepositoryInterface;

interface VideoRepositoryInterface extends EntityRepositoryInterface
{
    public function updateMedia(Entity $entity): Entity;
}
