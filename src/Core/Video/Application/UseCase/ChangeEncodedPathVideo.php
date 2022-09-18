<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    ChangeEncodedInputDTO,
    ChangeEncodedOutputDTO
};
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Media;

class ChangeEncodedPathVideo
{
    public function __construct(
        protected VideoRepositoryInterface $repository
    ) {
    }

    public function execute(ChangeEncodedInputDTO $input): ChangeEncodedOutputDTO
    {
        $entity = $this->repository->findById($input->id);
        $entity->setVideoFile(
            new Media(
                filePath: $entity->videoFile()?->filePath ?? '',
                mediaStatus: MediaStatus::COMPLETE,
                encodedPath: $input->encodedPath
            )
        );

        $this->repository->updateMedia($entity);

        return new ChangeEncodedOutputDTO(
            id: $entity->id(),
            encodedPath: $input->encodedPath
        );
    }
}
