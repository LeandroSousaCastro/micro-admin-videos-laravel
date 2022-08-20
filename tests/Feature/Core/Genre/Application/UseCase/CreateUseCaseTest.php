<?php

namespace Tests\Feature\Core\Genre\Application\UseCase;

use App\Models\Genre as ModelGenre;
use App\Models\Category as ModelCategory;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DbTransaction;
use Core\Genre\Application\UseCase\CreateUseCase as GenreCreateUseCase;
use Core\Genre\Application\Dto\CreateInputDto as GenreCreateInputDto;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Mockery;
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreate()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreCreateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        $response = $useCase->execute(
            new GenreCreateInputDto(
                name: 'Genre',
                categoriesId: $categoriesIds
            )
        );
        $this->assertEquals('Genre', $response->name);
        $this->assertNotNull($response->id);
        $this->assertDatabaseHas('genres', [
            'name' => 'Genre'
        ]);
        $this->assertDatabaseCount('category_genre', 10);
    }

    public function testCreateGenreWithInvalidCategoryIdI()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Categories fakeId, fakeId2 not found');
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreCreateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        array_push($categoriesIds, 'fakeId', 'fakeId2');

        $useCase->execute(
            new GenreCreateInputDto(
                name: 'Genre',
                categoriesId: $categoriesIds
            )
        );
    }

    public function testTransactionsCreateGenre()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreCreateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        try {
            $useCase->execute(
                new GenreCreateInputDto(
                    name: 'Genre',
                    categoriesId: $categoriesIds
                )
            );
            $this->assertDatabaseHas('genres', [
                'name' => 'Genre'
            ]);
            $this->assertDatabaseCount('category_genre', 10);
        } catch (\Exception $e) {
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
