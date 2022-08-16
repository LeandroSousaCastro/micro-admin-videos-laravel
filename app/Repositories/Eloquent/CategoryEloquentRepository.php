<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Category\Seedwork\Repository\PaginationInterface;
use Core\Category\Domain\Entity\Category as EntityCategory;
use Core\Category\Domain\Repository\RepositoryInterface as CategoryRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{

    public function __construct(CategoryModel $model)
    {
        $this->model = $model;
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
            throw new NotFoundException();
        }

        return $this->toCategory($category);
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
            $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query->orderBy('id', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(EntityCategory $EntityCategory): EntityCategory
    {
        if (!$category = $this->model->find($EntityCategory->id())) {
            throw new NotFoundException();
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
            throw new NotFoundException();
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