<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Category\Domain\Entity\Category;
use Core\Seedwork\Domain\Entity\Entity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{

    public function __construct(protected CategoryModel $model)
    {
    }

    public function insert(Entity $category): Entity
    {
        $category = $this->model->create([
            'id' => $category->id(),
            'name' => $category->name,
            'description' => $category->description ?? null,
            'is_active' => $category->isActive,
            'created_at' => $category->createdAt()
        ]);
        return $this->toCategory($category);
    }

    public function findById(string $id): Entity
    {
        if (!$category = $this->model->find($id)) {
            throw new NotFoundException("Category not found for id: {$id}");
        }

        return $this->toCategory($category);
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
            ->orderBy('name', $order)
            ->get();
        return $categories->toArray();
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

    public function update(Entity $entityCategory): Entity
    {
        if (!$category = $this->model->find($entityCategory->id())) {
            throw new NotFoundException("Category not found for id: {$entityCategory->id()}");
        }

        $category->update([
            'name' => $entityCategory->name,
            'description' => $entityCategory->description,
            'is_active' => $entityCategory->isActive
        ]);

        $category->refresh();

        return $this->toCategory($category);
    }

    public function delete(string $id): bool
    {
        if (!$category = $this->model->find($id)) {
            throw new NotFoundException("Category not found for id: {$id}");
        }

        $result = $category->delete();
        $category->refresh();
        return $result;
    }

    private function toCategory(object $data): Entity
    {
        $entity =  new Category(
            name: $data->name,
            description: $data->description,
            id: new Uuid($data->id),
        );
        ((bool) $data->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
