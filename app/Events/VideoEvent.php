<?php

namespace App\Events;

use Core\Seedwork\Application\Interfaces\EventManagerInterface;

class VideoEvent implements EventManagerInterface
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
