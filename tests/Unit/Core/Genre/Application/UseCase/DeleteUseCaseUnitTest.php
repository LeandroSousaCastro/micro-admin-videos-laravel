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

        $this->mockEntity = Mockery::mock(Genre::class, [
            $id,
            $name,
            [],
            true
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($id);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $this->mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepository->shouldReceive('findById')
            ->with($id)
            ->andReturn($this->mockEntity);
        $this->mockRepository->shouldReceive('delete')
            ->with($id)
            ->andReturn(true);

        $this->mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $id
        ]);

        $useCase = new DeleteUseCase($this->mockRepository);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(DeleteUseCase::class, $useCase);
        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $this->mockRepository->shouldHaveReceived('delete')->once();

        Mockery::close();
    }
}
