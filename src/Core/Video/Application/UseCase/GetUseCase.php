<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Video\Domain\Repository\VideoRepositoryInterface;

class GetUseCase
{
    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(GetInputDto $input): GetOutputDto
    {
        $entity = $this->repository->findById($input->id);

        return new GetOutputDto(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            createdAt: $entity->createdAt(),
            categories: $entity->categoriesId,
            genres: $entity->genresId,
            castMembers: $entity->castMembersId,
            thumbFile: $entity->thumbFile()?->path(),
            thumbHalf: $entity->thumbHalf()?->path(),
            bannerFile: $entity->bannerFile()?->path(),
            trailerFile: $entity->trailerFile()?->filePath,
            videoFile: $entity->videoFile()?->filePath,
        );
    }
}
