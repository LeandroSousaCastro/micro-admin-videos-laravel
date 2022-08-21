<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Category\Domain\Entity\Category as EntityCategory;
use Core\Category\Domain\Repository\CategoryRepositoryInterface as CategoryCategoryRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;

class CategoryEloquentRepository implements CategoryCategoryRepositoryInterface
{

    public function __construct(protected CategoryModel $model)
    {
    }

    public function insert(EntityCategory $category): EntityCategory
    {
        $category = $this->model->create([
            'id' => $category->id(),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive,
            'created_at' => $category->createdAt()
        ]);
        return $this->toCategory($category);
    }

    public function findById(string $id): EntityCategory
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
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(EntityCategory $EntityCategory): EntityCategory
    {
        if (!$category = $this->model->find($EntityCategory->id())) {
            throw new NotFoundException("Category not found for id: {$EntityCategory->id()}");
        }

        $category->update([
            'name' => $EntityCategory->name,
            'description' => $EntityCategory->description,
            'is_active' => $EntityCategory->isActive
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

    private function toCategory(object $data): EntityCategory
    {
        $entity =  new EntityCategory(
            id: $data->id,
            name: $data->name,
            description: $data->description,
        );
        ((bool) $data->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }
}
