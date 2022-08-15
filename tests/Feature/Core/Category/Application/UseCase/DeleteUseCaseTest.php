<?php

namespace Tests\Feature\Core\Category\Application\UseCase;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\DeleteUseCase as CategoryDeleteUseCase;
use Core\Category\Application\Dto\DeleteInputDto as CategoryDeleteInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testDelete()
    {
        $category = CategoryModel::factory()->create();
        $repository = new CategoryEloquentRepository(new CategoryModel());
        $useCase = new CategoryDeleteUseCase($repository);
        $useCase->execute(
            new CategoryDeleteInputDto(
                id: $category->id
            )
        );
        $this->assertSoftDeleted($category);
    }
}
