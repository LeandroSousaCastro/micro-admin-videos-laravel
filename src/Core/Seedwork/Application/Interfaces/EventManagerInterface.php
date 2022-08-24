<?php

namespace Core\Seedwork\Application\Interfaces;

interface EventManagerInterface
{
    public function dispatch(object $event): void;
}
