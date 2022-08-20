<?php

namespace Tests\Feature\Core\Genre\Application\UseCase;

use App\Models\Genre as ModelGenre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Genre\Application\Dto\GetInputDto;
use Core\Genre\Application\UseCase\GetUseCase;
use Tests\TestCase;

class GetUseCaseTest extends TestCase
{
    public function testFindById()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $useCase = new GetUseCase($repository);

        $genre = ModelGenre::factory()->create();

        $response = $useCase->execute(
            new GetInputDto(
                id: $genre->id
            )
        );
        $this->assertEquals($genre->id, $response->id);
        $this->assertEquals($genre->name, $response->name);
    }
}
