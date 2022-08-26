<?php

namespace Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Video\Domain\Builder\BuilderInterface;
use Core\Video\Domain\Builder\UpdateVideoBuilder;
use Throwable;

class UpdateUseCase extends BaseUseCase
{
    protected function getBuilder(): BuilderInterface
    {
        return new UpdateVideoBuilder();
    }

    public function execute(UpdateInputDto $input): UpdateOutputDto
    {
        $this->validateAllIds($input);

        $entity = $this->repository->findById($input->id);
        $entity->update(
            title: $input->title,
            description: $input->description,
        );

        $this->builder->setEntity($entity);

        try {
            $this->repository->update($this->builder->getEntity());
            $this->storageFiles($input);
            $this->repository->updateMedia($this->builder->getEntity());
            $this->transaction->commit();
            return $this->output();
        } catch (Throwable $th) {
            $this->transaction->rollBack();
            throw $th;
        }
    }

    private function output(): UpdateOutputDto
    {
        $entity = $this->builder->getEntity();
        return new UpdateOutputDto(
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
            thumbFile: $entity->thumbFile()?->path(),
            thumbHalf: $entity->thumbHalf()?->path(),
            bannerFile: $entity->bannerFile()?->path(),
            trailerFile: $entity->trailerFile()?->filePath,
            videoFile: $entity->videoFile()?->filePath,
        );
    }
}
