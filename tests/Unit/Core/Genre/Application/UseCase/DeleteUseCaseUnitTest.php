<?php

namespace Tests\Unit\Core\Genre\Application\UseCase;

use Core\Genre\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto
};
use Core\Genre\Application\UseCase\DeleteUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $id = Uuid::random();
        $name = 'name';

        $mockEntity = Mockery::mock(Genre::class, [
            $name,
            [],
            true,
            $id,
        ]);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->times(1)
            ->with($id->__toString())
            ->andReturn(true);

        $mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $id->__toString()
        ]);

        $useCase = new DeleteUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteUseCase::class, $useCase);
        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $mockRepository->shouldHaveReceived('delete')->once();
    }

    public function testFailDelete()
    {
        $uuid = Uuid::random();

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->times(1)
                        ->with($uuid->__toString())
                        ->andReturn(false);

        $mockInputDto = Mockery::mock(DeleteInputDto::class, [$uuid->__toString()]);

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
