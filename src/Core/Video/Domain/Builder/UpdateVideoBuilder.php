<?php

namespace Core\Video\Domain\Builder;

use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Entity\Video;

class UpdateVideoBuilder extends VideoBuilder
{
    public function createEntity(object $input): BuilderInterface
    {
        $this->entity = new Video(
            id: new Uuid($input->id),
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
            createdAt: new \DateTime($input->createdAt)
        );

        $this->addIds($input);

        return $this;
    }
}
