<?php

namespace Core\Seedwork\Domain\Entity;

use Core\Seedwork\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Seedwork\Domain\Notification\Notification;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Laminas\Hydrator\ReflectionHydrator;

abstract class Entity
{
    use MethodsMagicsTrait;

    protected Notification $notification;
    protected array $rules = [];

    public function __construct(
        protected Uuid|string $id = '',
        protected \DateTime|string $createdAt = ''
    ) {
        $this->id = $this->id === '' ? Uuid::random() : $this->id;
        $this->createdAt = $this->createdAt ? $this->createdAt : new \DateTime();
        $this->notification = new Notification();
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function createdAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function toArray()
    {
        return (new ReflectionHydrator)->extract($this);
    }
}
