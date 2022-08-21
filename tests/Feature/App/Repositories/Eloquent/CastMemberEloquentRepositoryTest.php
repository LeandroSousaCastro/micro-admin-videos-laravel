<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use App\Models\CastMember as CastMemberModel;
use Core\CastMember\Domain\Entity\CastMember as EntityCastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Tests\TestCase;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new EntityCastMember(
            name: 'CastMember',
            type: CastMemberType::ACTOR
        );
        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CastMemberEloquentRepository::class, $this->repository);
        $this->assertInstanceOf(EntityCastMember::class, $response);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id(),
            'name' => 'CastMember',
            'type' => CastMemberType::ACTOR->value
        ]);
        $this->assertEquals($response->id, $entity->id);
        $this->assertEquals($response->id(), $entity->id());
        $this->assertEquals($response->name, $entity->name);
        $this->assertEquals($response->type, $entity->type);
    }


    public function testFindById()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->repository->findById($castMember->id);
        $this->assertInstanceOf(EntityCastMember::class, $response);
        $this->assertEquals($castMember->id, $response->id());
        $this->assertEquals($castMember->name, $response->name);
        $this->assertEquals($castMember->type, $response->type);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('CastMember not found for id: id fake');
        $this->repository->findById('id fake');
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertEmpty($response);
        $this->assertCount(0, $response);
    }

    public function testFindAll()
    {
        CastMemberModel::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
    }

    public function testPaginate()
    {
        CastMemberModel::factory()->count(20)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateWithoutItems()
    {
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('CastMember not found for id: id fake');
        $entity = new EntityCastMember(
            id: 'id fake',
            name: 'CastMember',
            type: CastMemberType::ACTOR
        );
        $this->repository->update($entity);
    }

    public function testUpdate()
    {
        $castMember = CastMemberModel::factory()->create();
        $entity = new EntityCastMember(
            id: $castMember->id,
            name: 'CastMember',
            type: CastMemberType::ACTOR
        );

        $name = 'CastMember updated';
        $entity->update(
            name: $name,
            type: CastMemberType::DIRECTOR
        );

        $response = $this->repository->update($entity);
        $this->assertInstanceOf(EntityCastMember::class, $response);
        $this->assertEquals($castMember->id, $response->id());
        $this->assertEquals($name, $response->name);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->id(),
            'name' => $name,
            'type' => CastMemberType::DIRECTOR->value
        ]);
    }

    public function testDelete()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->repository->delete($castMember->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted($castMember);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('id fake');
    }
}
