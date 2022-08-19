<?php

namespace Tests\Unit\Genre\Domain\Application\UseCase;

use Core\Genre\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto
};
use Core\Genre\Application\UseCase\DeleteUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseUnitTest extends TestCase
{
    public function testDeleteUseCase()
    {
        $id = Uuid::uuid4()->toString();
        $name = 'name';

        $mockEntity = Mockery::mock(Genre::class, [
            $id,
            $name,
            [],
            true
        ]);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->with($id)
            ->andReturn($mockEntity);
        $mockRepository->shouldReceive('delete')
            ->with($id)
            ->andReturn(true);

        $mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $id
        ]);

        $useCase = new DeleteUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteUseCase::class, $useCase);
        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $mockRepository->shouldHaveReceived('delete')->once();

        Mockery::close();
    }

    public function testFailDeleteUseCase()
    {
        $uuid = (string) Uuid::uuid4();

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->times(1)
                        ->with($uuid)
                        ->andReturn(false);

        $mockInputDto = Mockery::mock(DeleteInputDto::class, [$uuid]);

        $useCase = new DeleteUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertFalse($response->isSuccess);
        $mockRepository->shouldHaveReceived('delete')->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
