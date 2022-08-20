<?php

namespace Tests\Feature\Core\Genre\Application\UseCase;

use App\Models\Genre as ModelGenre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Genre\Application\Dto\ListInputDto;
use Core\Genre\Application\Dto\ListOutputDto;
use Core\Genre\Application\UseCase\ListUseCase;
use Tests\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testFindAll()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $useCase = new ListUseCase($repository);

        ModelGenre::factory()->count(100)->create();

        $response = $useCase->execute(
            new ListInputDto()
        );
        $this->assertInstanceOf(ListOutputDto::class, $response);
        $this->assertCount(15, $response->items);
        $this->assertEquals(100, $response->total);
    }
}
