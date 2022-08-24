<?php

namespace Core\Video\Domain\Events;

use Core\Seedwork\Domain\Events\EventInterface;
use Core\Video\Domain\Entity\Video;

class VideoCreated implements EventInterface
{
    public function __construct(protected Video $video)
    {
    }

    public function getEventName(): string
    {
        return 'video.created';
    }

    public function getPayload(): array
    {
        return [
            'resource_id' => $this->video->di,
            'file_path' => $this->video->videoFile()->path
        ];
    }
}
