<?php

namespace Core\Seedwork\Domain\Events;

interface EventInterface
{
    public function getEventName(): string;
    public function getPayload(): array;
}
