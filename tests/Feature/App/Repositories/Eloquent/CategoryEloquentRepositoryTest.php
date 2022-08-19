<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Seedwork\Domain\Exception\NotFoundException;
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Domain\Entity\Category as EntityCategory;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new CategoryModel());
    }

    public function testInsert()
    {
        $entity = new EntityCategory(
            name: 'Category'
        );
        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CategoryEloquentRepository::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'name' => 'Category'
        ]);
    }


    public function testFindById()
    {
        $category = CategoryModel::factory()->create();
        $response = $this->repository->findById($category->id);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
        $this->assertEquals($category->name, $response->name);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->findById('id fake');
    }

    public function testFindAll()
    {
        CategoryModel::factory()->count(10)->create();
        $response = $this->repository->findAll();        
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
    }

    public function testPaginate()
    {
        CategoryModel::factory()->count(20)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateWithout()
    {
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdate()
    {
        $category = CategoryModel::factory()->create();
        $entity = new EntityCategory(
            id: $category->id,
            name: 'Category'
        );

        $entity->update(
            name: 'Category Update'
        );

        $response = $this->repository->update($entity);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
        $this->assertEquals('Category Update', $response->name);
        $this->assertDatabaseHas('categories', [
            'name' => 'Category Update'
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category not found for id: id fake');
        $entity = new EntityCategory(
            id: 'id fake',
            name: 'Category'
        );
        $this->repository->update($entity);
    }

    public function testDelete()
    {
        $category = CategoryModel::factory()->create();
        $response = $this->repository->delete($category->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted($category);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('id fake');
    }
}