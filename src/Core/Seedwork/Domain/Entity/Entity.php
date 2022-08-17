<?php 

namespace Core\Seedwork\Domain\Entity;

use Core\Seedwork\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Seedwork\Domain\ValueObject\Uuid;

abstract class Entity
{
    use MethodsMagicsTrait;

    public function __construct(
        protected Uuid|string $id = '',
        protected \DateTime|string $createdAt = ''
    ) {
        $this->id = $this->id ? $this->id : Uuid::random();
        $this->createdAt = $this->createdAt ? $this->createdAt : new \DateTime();
    }
}
