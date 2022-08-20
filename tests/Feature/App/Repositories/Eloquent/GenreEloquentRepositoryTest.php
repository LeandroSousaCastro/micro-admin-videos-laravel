<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category;
use Core\Seedwork\Domain\Exception\NotFoundException;
use App\Models\Genre as ModelGenre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Genre\Domain\Entity\Genre as EntityGenre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new ModelGenre());
    }

    public function testImplementInterface()
    {
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new EntityGenre(
            name: 'Genre'
        );
        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(GenreEloquentRepository::class, $this->repository);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'name' => 'Genre'
        ]);
    }

    public function testInsertDeactivate()
    {
        $entity = new EntityGenre(
            name: 'Genre',
        );
        $entity->deactivate();

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertDatabaseHas('genres', [
            'name' => 'Genre',
            'is_active' => false
        ]);
    }

    public function testInsertWithRelationships()
    {
        $entity = new EntityGenre(
            name: 'Genre',
        );
        $categories = Category::factory()->count(4)->create();
        $categoriesId = $categories->pluck('id')->toArray();
        foreach ($categoriesId as $categoryId) {
            $entity->addCategory($categoryId);
        }
        $response = $this->repository->insert($entity);

        $this->assertEquals($categoriesId, $entity->categoriesId);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id(),
        ]);
        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Genre not found for id: id fake');
        $this->repository->findById('id fake');
    }

    public function testFindById()
    {
        $genre = ModelGenre::factory()->create();
        $response = $this->repository->findById($genre->id);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertEquals($genre->id, $response->id());
        $this->assertEquals($genre->name, $response->name);
    }

    public function testFindAll()
    {
        ModelGenre::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertEquals([], $response);
        $this->assertCount(0, $response);
    }

    public function testFindAllWithFilter()
    {
        ModelGenre::factory()->count(10)->create([
            'name' => 'Genre'
        ]);
        ModelGenre::factory()->count(10)->create();
        $response = $this->repository->findAll(
            filter: 'Genre'
        );
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
        $responseWithoutFilter = $this->repository->findAll();
        $this->assertCount(20, $responseWithoutFilter);
    }

    public function testPaginate()
    {
        ModelGenre::factory()->count(20)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
        $this->assertEquals(2, $response->lastPage());
        $this->assertEquals(20, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->currentPage());
    }

    public function testPaginateEmpty()
    {
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(0, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->currentPage());
    }

    public function testUpdate()
    {
        $genre = ModelGenre::factory()->create();
        $entity = new EntityGenre(
            id: $genre->id,
            name: 'Genre',
            isActive: (bool) $genre->is_active,
            createdAt: new \DateTime($genre->created_at)
        );

        $entity->update(
            name: 'Genre Update'
        );
        $response = $this->repository->update($entity);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertEquals($genre->id, $response->id());
        $this->assertEquals('Genre Update', $response->name);
        $this->assertDatabaseHas('genres', [
            'name' => 'Genre Update'
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Genre not found for id: id fake');
        $entity = new EntityGenre(
            id: 'id fake',
            name: 'Genre'
        );
        $this->repository->update($entity);
    }

    public function testDelete()
    {
        $genre = ModelGenre::factory()->create();
        $response = $this->repository->delete($genre->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted($genre);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('id fake');
    }
}