<?php

namespace Tests\Stubs;

use Core\Video\Domain\Events\VideoEventManagerInterface;

class VideoEventStub implements VideoEventManagerInterface
{
    public function dispatch(object $event): void
    {
        // NO IMPLEMENTATION NEEDED
    }
}
