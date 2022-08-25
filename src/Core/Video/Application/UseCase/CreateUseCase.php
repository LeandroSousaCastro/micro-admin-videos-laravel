<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Throwable;

class CreateUseCase extends BaseUseCase
{
    public function execute(CreateInputDto $input): CreateOutputDto
    {
        $this->validateAllIds($input);
        $this->builder->createEntity($input);

        try {
            $this->repository->insert($this->builder->getEntity());
            $this->storageFiles($input);
            $this->repository->updateMedia($this->builder->getEntity());
            $this->transaction->commit();
            return $this->output();
        } catch (Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }
    }

    private function output(): CreateOutputDto
    {
        $entity = $this->builder->getEntity();
        return new CreateOutputDto(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            categories: $entity->categoriesId,
            genres: $entity->genresId,
            castMembers: $entity->castMembersId,
            thumbFile: $entity->thumbFile()?->filePath,
            thumbHalf: $entity->thumbHalf()?->filePath,
            bannerFile: $entity->bannerFile()?->filePath,
            trailerFile: $entity->trailerFile()?->filePath,
            videoFile: $entity->videoFile()?->filePath,
        );
    }
}
