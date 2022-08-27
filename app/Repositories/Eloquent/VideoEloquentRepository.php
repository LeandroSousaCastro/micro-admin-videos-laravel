<?php

namespace App\Repositories\Eloquent;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use App\Models\Video as ModelVideo;
use App\Repositories\Eloquent\Traits\VideoTrait;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Video\Domain\Entity\Video as VideoEntity;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Builder\UpdateVideoBuilder;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    use VideoTrait;

    public function __construct(protected ModelVideo $model)
    {
    }

    public function insert(Entity $entity): Entity
    {
        $videoModel = $this->model->create([
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);

        $this->syncRelationships($videoModel, $entity);

        return $this->convertObjectToEntity($videoModel);
    }

    public function findById(string $id): Entity
    {
        if (!$video = $this->model->find($id)) {
            throw new NotFoundException("Video not found for id: {$id}");
        }

        return $this->convertObjectToEntity($video);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $videos = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('title', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('title', $order)
            ->get();
        return $videos->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $result = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('title', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('title', $order)
            ->paginate($totalPage, ['*'], 'page', $page);

        return new PaginationPresenter($result);
    }

    public function update(Entity $entityVideo): Entity
    {
        if (!$videoModel = $this->model->find($entityVideo->id())) {
            throw new NotFoundException("Video not found for id: {$entityVideo->id()}");
        }

        $videoModel->update([
            'title' => $entityVideo->title,
            'description' => $entityVideo->description,
            'year_launched' => $entityVideo->yearLaunched,
            'rating' => $entityVideo->rating->value,
            'duration' => $entityVideo->duration,
            'opened' => $entityVideo->opened,
        ]);

        $videoModel->refresh();

        $this->syncRelationships($videoModel, $entityVideo);

        return $this->convertObjectToEntity($videoModel);
    }

    public function delete(string $id): bool
    {
        if (!$video = $this->model->find($id)) {
            throw new NotFoundException("Video not found for id: {$id}");
        }

        $result = $video->delete();
        $video->refresh();
        return $result;
    }

    public function updateMedia(Entity $entity): Entity
    {
        if (!$videoModel = $this->model->find($entity->id())) {
            throw new NotFoundException("Video not found for id: {$entity->id()}");
        }

        $this->model = $videoModel;

        $this->updateMediaVideo($entity);
        $this->updateMediaTrailer($entity);
        $this->updateImageBanner($entity);
        $this->updateImageThumb($entity);
        $this->updateImageThumbHalf($entity);

        return $this->convertObjectToEntity($videoModel);
    }

    protected function syncRelationships(ModelVideo $model, Entity $entity): void
    {
        $model->categories()->sync($entity->categoriesId);
        $model->genres()->sync($entity->genresId);
        $model->castMembers()->sync($entity->castMembersId);
    }

    protected function convertObjectToEntity(object $model): VideoEntity
    {
        $entity = new VideoEntity(
            id: new Uuid($model->id),
            title: $model->title,
            description: $model->description,
            yearLaunched: (int) $model->year_launched,
            rating: Rating::from($model->rating),
            duration: (bool) $model->duration,
            opened: $model->opened
        );

        foreach ($model->categories as $category) {
            $entity->addCategoryId($category->id);
        }

        foreach ($model->genres as $genre) {
            $entity->addGenreId($genre->id);
        }

        foreach ($model->castMembers as $castMember) {
            $entity->addCastMemberId($castMember->id);
        }

        if ($video = $model->media) {
            $entity->setVideoFile(new Media(
                filePath: $video->file_path,
                mediaStatus: MediaStatus::from($video->media_status),
                encodedPath: $video->encoded_path
            ));
        }

        if ($trailer = $model->trailer) {
            $entity->setTrailerFile(new Media(
                filePath: $trailer->file_path,
                mediaStatus: MediaStatus::from($trailer->media_status),
                encodedPath: $trailer->encoded_path
            ));
        }

        if ($banner = $model->banner) {
            $entity->setBannerFile(new Image(
                path: $banner->path
            ));
        }

        if ($thumb = $model->thumb) {
            $entity->setThumbFile(new Image(
                path: $thumb->path
            ));
        }

        if ($thumbHalf = $model->thumbHalf) {
            $entity->setThumbHalf(new Image(
                path: $thumbHalf->path
            ));
        }

        $builder = (new UpdateVideoBuilder())
            ->setEntity($entity);

        if ($trailer = $model->trailer) {
            $builder->addTrailer($trailer->file_path);
        }

        if ($mediaVideo = $model->media) {
            $builder->addMediaVideo(
                path: $mediaVideo->file_path,
                mediaStatus: MediaStatus::from($mediaVideo->media_status),
                encodedPath: $mediaVideo->encoded_path
            );
        }

        if ($banner = $model->banner) {
            $builder->addBanner($banner->path);
        }

        if ($thumb = $model->thumb) {
            $builder->addThumb($thumb->path);
        }

        if ($thumbHalf = $model->thumbHalf) {
            $builder->addThumbHalf($thumbHalf->path);
        }

        return $builder->getEntity();
    }
}
