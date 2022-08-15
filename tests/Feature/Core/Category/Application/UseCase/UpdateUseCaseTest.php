<?php

namespace Tests\Feature\Core\Category\Application\UseCase;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\UpdateUseCase as CategoryUpdateUseCase;
use Core\Category\Application\Dto\UpdateInputDto as CategoryUpdateInputDto;
use Tests\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testUpdate()
    {
        $category = CategoryModel::factory()->create();
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CategoryUpdateUseCase($repository);
        $response = $useCase->execute(
            new CategoryUpdateInputDto(
                id: $category->id,
                name: 'Category',
            )
        );
        $this->assertEquals('Category', $response->name);
        $this->assertEquals($category->description, $response->description);
        $this->assertDatabaseHas('categories', [
            'name' => 'Category',
        ]);
    }
}
