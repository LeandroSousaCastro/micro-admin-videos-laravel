<?php

namespace Tests\Feature\Core\Category\Application\UseCase;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\GetUseCase as CategoryGetUseCase;
use Core\Category\Application\Dto\GetInputDto as CategoryGetInputDto;
use Core\Category\Application\Dto\GetOutputDto as CategoryGetOutputDto;
use Tests\TestCase;

class GetUseCaseTest extends TestCase
{
    public function testGet() {
        $category = CategoryModel::factory()->create();
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CategoryGetUseCase($repository);
        $response = $useCase->execute(new CategoryGetInputDto(
            id: $category->id
        ));

        $this->assertInstanceOf(CategoryGetOutputDto::class, $response);
        $this->assertEquals($category->id, $response->id);
        $this->assertEquals($category->name, $response->name);
        $this->assertEquals($category->description, $response->description);
    }
}
