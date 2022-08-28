<?php

namespace Tests\Stubs;

use Core\Video\Domain\Events\VideoEventManagerInterface;

class VideoEventStub implements VideoEventManagerInterface
{
    public function __construct()
    {
        event($this);
    }

    public function dispatch(object $event): void
    {
        // NO IMPLEMENTATION NEEDED
    }
}
