<?php

namespace Tests\Feature\Core\Category\Application\UseCase;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\CreateUseCase as CategoryCreateUseCase;
use Core\Category\Application\Dto\CreateInputDto as CategoryCreateInputDto;
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreate()
    {
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CategoryCreateUseCase($repository);
        $response = $useCase->execute(
            new CategoryCreateInputDto(
                name: 'Category'
            )
        );
        $this->assertEquals('Category', $response->name);
        $this->assertNotNull($response->id);
        $this->assertDatabaseHas('categories', [
            'name' => 'Category'
        ]);
    }
}
