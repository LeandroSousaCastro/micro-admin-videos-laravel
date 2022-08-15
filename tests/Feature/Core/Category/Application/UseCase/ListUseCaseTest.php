<?php

namespace Tests\Feature\Core\Category\Application\UseCase;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\ListUseCase as CategoryListUseCase;
use Core\Category\Application\Dto\ListInputDto as CategoryListInputDto;
use Core\Category\Application\Dto\ListOutputDto as CategoryListOutputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testListEmpty()
    {
        $response = $this->createUseCase();
        $this->assertCount(0, $response->items);
    }

    public function testListAll() {
        $categories = CategoryModel::factory()->count(20)->create();
        $response = $this->createUseCase();
        $this->assertCount(15, $response->items);
        $this->assertEquals(count($categories), $response->total);
    }

    private function createUseCase(): CategoryListOutputDto {
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CategoryListUseCase($repository);
        return $useCase->execute(new CategoryListInputDto());   
    }
}
