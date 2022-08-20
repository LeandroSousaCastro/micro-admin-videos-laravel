<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as ModelGenre;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Genre\Domain\Entity\Genre as EntityGenre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;

class GenreEloquentRepository implements GenreRepositoryInterface
{

    public function __construct(protected ModelGenre $model)
    {
    }

    public function insert(EntityGenre $entity): EntityGenre
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

    public function findById(string $id): EntityGenre
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException("Genre not found for id: {$id}");
        }

        return $this->toGenre($genre);
    }

    public function getIdsListIds(array $categoriesId = []): array
    {
        return $this->model->whereIn('id', $categoriesId)->pluck('id')->toArray();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $categories = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('id', $order)
            ->get();
        return $categories->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query = $query->orderBy('created_at', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(EntityGenre $entityGenre): EntityGenre
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

    private function toGenre(object $object): EntityGenre
    {
        $entity =  new EntityGenre(
            id: $object->id,
            name: $object->name,
            createdAt: $object->created_at,
        );
        ((bool) $object->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
