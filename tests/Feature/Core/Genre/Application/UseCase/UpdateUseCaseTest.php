<?php

namespace Tests\Feature\Core\Genre\Application\UseCase;

use App\Models\Genre as ModelGenre;
use App\Models\Category as ModelCategory;
use App\Repositories\Eloquent\{
    CategoryEloquentRepository,
    GenreEloquentRepository
};
use App\Repositories\Transaction\DbTransaction;
use Core\Genre\Application\UseCase\UpdateUseCase as GenreUpdateUseCase;
use Core\Genre\Application\Dto\UpdateInputDto as GenreUpdateInputDto;
use Core\Genre\Application\Dto\UpdateOutputDto;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Tests\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testCreate()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreUpdateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $genre = ModelGenre::factory()->create();

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        $response = $useCase->execute(
            new GenreUpdateInputDto(
                id: $genre->id,
                name: 'New Genre',
                categoriesId: $categoriesIds
            )
        );

        $this->assertInstanceOf(UpdateOutputDto::class, $response);
        $this->assertDatabaseHas('genres', [
            'name' => 'New Genre',
        ]);
        $this->assertDatabaseCount('category_genre', 10);
    }

    public function testCreateGenreWithInvalidCategoryIdI()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Categories fakeId, fakeId2 not found');
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreUpdateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $genre = ModelGenre::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        array_push($categoriesIds, 'fakeId', 'fakeId2');

        $useCase->execute(
            new GenreUpdateInputDto(
                id: $genre->id,
                name: 'Genre',
                categoriesId: $categoriesIds
            )
        );
    }

    public function testTransactionsCreateGenre()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new GenreUpdateUseCase(
            $repository,
            $repositoryCategory,
            new DbTransaction()
        );

        $genre = ModelGenre::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        try {
            $useCase->execute(
                new GenreUpdateInputDto(
                    id: $genre->id,
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
