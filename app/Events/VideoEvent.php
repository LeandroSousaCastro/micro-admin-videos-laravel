<?php

namespace App\Events;

use Core\Video\Domain\Events\VideoEventManagerInterface;

class VideoEvent implements VideoEventManagerInterface
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
