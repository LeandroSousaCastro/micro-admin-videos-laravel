<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as ModelCastMember;
use App\Repositories\Presenters\PaginationPresenter;
use Core\CastMember\Domain\Entity\CastMember as EntityCastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\Repository\PaginationInterface;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{

    public function __construct(protected ModelCastMember $model)
    {
    }

    public function insert(EntityCastMember $castMember): EntityCastMember
    {
        $castMember = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt()
        ]);
        return $this->toCastMember($castMember);
    }

    public function findById(string $id): EntityCastMember
    {
        if (!$castMember = $this->model->find($id)) {
            throw new NotFoundException("CastMember not found for id: {$id}");
        }

        return $this->toCastMember($castMember);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $castMembers = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->get();
        return $castMembers->toArray();
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

    public function update(EntityCastMember $EntityCastMember): EntityCastMember
    {
        if (!$castMember = $this->model->find($EntityCastMember->id())) {
            throw new NotFoundException("CastMember not found for id: {$EntityCastMember->id()}");
        }

        $castMember->update([
            'name' => $EntityCastMember->name,
            'type' => $EntityCastMember->type->value,
        ]);

        $castMember->refresh();

        return $this->toCastMember($castMember);
    }

    public function delete(string $id): bool
    {
        if (!$castMember = $this->model->find($id)) {
            throw new NotFoundException("CastMember not found for id: {$id}");
        }

        $result = $castMember->delete();
        $castMember->refresh();
        return $result;
    }

    private function toCastMember(object $object): EntityCastMember
    {
        return new EntityCastMember(
            id: $object->id,
            name: $object->name,
            type: CastMemberType::from($object->type),
            createdAt: $object->created_at
        );
    }
}
