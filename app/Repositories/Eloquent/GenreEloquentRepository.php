<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as ModelGenre;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Genre\Domain\Entity\Genre;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;

class GenreEloquentRepository implements GenreRepositoryInterface
{

    public function __construct(protected ModelGenre $model)
    {
    }

    public function insert(Entity $entity): Entity
    {
        $genre = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt()
        ]);

        if (count($entity->categoriesId) > 0) {
            $genre->categories()->sync($entity->categoriesId);
        }

        return $this->toGenre($genre);
    }

    public function findById(string $id): Entity
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException("Genre not found for id: {$id}");
        }

        return $this->toGenre($genre);
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
        $paginator = $query->paginate($totalPage, ['*'], 'page', $page);

        return new PaginationPresenter($paginator);
    }

    public function update(Entity $entityGenre): Entity
    {
        if (!$genre = $this->model->find($entityGenre->id())) {
            throw new NotFoundException("Genre not found for id: {$entityGenre->id()}");
        }

        $genre->update([
            'name' => $entityGenre->name,
            'is_active' => $entityGenre->isActive
        ]);

        if (count($entityGenre->categoriesId) > 0) {
            $genre->categories()->sync($entityGenre->categoriesId);
        }

        $genre->refresh();

        return $this->toGenre($genre);
    }

    public function delete(string $id): bool
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException("Genre not found for id: {$id}");
        }

        $result = $genre->delete();
        $genre->refresh();
        return $result;
    }

    private function toGenre(object $object): Entity
    {
        $entity =  new Genre(
            name: $object->name,
            id: new Uuid($object->id),
            createdAt: $object->created_at,
        );
        ((bool) $object->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
