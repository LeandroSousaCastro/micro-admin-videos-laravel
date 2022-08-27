<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as ModelVideo;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Video\Domain\Entity\Video;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;

class VideoEloquentRepository implements VideoRepositoryInterface
{

    public function __construct(protected ModelVideo $model)
    {
    }

    public function insert(Entity $entity): Entity
    {
        $genre = $this->model->create([
            // TODO
        ]);

        if (count($entity->categoriesId) > 0) {
            $genre->categories()->sync($entity->categoriesId);
        }

        return $this->toVideo($genre);
    }

    public function findById(string $id): Entity
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException("Video not found for id: {$id}");
        }

        return $this->toVideo($genre);
    }

    public function getIdsListIds(array $genresId = []): array
    {
        return $this->model->whereIn('id', $genresId)->pluck('id')->toArray();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $genres = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->get();
        return $genres->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('name', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(Entity $entityVideo): Entity
    {
        if (!$genre = $this->model->find($entityVideo->id())) {
            throw new NotFoundException("Video not found for id: {$entityVideo->id()}");
        }

        $genre->update([
            'name' => $entityVideo->name,
            'is_active' => $entityVideo->isActive
        ]);

        if (count($entityVideo->categoriesId) > 0) {
            $genre->categories()->sync($entityVideo->categoriesId);
        }

        $genre->refresh();

        return $this->toVideo($genre);
    }

    public function delete(string $id): bool
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException("Video not found for id: {$id}");
        }

        $result = $genre->delete();
        $genre->refresh();
        return $result;
    }

    public function updateMedia(Entity $entity): Entity
    {
        // TODO
    }

    private function toVideo(object $object): Entity
    {
        $entity =  new Video(
            // TODO
        );

        return $entity;
    }
}
