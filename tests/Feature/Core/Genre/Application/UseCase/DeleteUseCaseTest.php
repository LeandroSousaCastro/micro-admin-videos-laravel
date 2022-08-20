<?php

namespace Tests\Feature\Core\Genre\Application\UseCase;

use App\Models\Genre as ModelGenre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Genre\Application\Dto\DeleteInputDto;
use Core\Genre\Application\Dto\DeleteOutputDto;
use Core\Genre\Application\UseCase\DeleteUseCase;
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testDelete()
    {
        $repository = new GenreEloquentRepository(new ModelGenre());
        $useCase = new DeleteUseCase($repository);

        $genre = ModelGenre::factory()->create();

        $response = $useCase->execute(
            new DeleteInputDto(
                id: $genre->id
            )
        );
        $this->assertInstanceOf(DeleteOutputDto::class, $response);
        $this->assertTrue($response->isSuccess);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id,
        ]);
    }
}
