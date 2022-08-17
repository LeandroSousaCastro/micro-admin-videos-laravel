<?php 

namespace Core\Genre\Domain\Entity;

use Core\Seedwork\Domain\Entity\EntityBase;
use Core\Seedwork\Domain\ValueObject\Uuid;
use DateTime;

class Genre extends EntityBase
{
    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = '',
        protected bool $isActive = false,
        protected DateTime|string $createdAt = ''
    ) {
        parent::__construct($id, $createdAt);
    }
}