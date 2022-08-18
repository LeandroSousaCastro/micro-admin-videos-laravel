<?php

namespace Tests\Unit\Genre\Domain\Application\UseCase;

use Core\Genre\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Genre\Application\UseCase\GetUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUseCaseUnitTest extends TestCase
{
    public function testGetUseCase()
    {
        $id = Uuid::uuid4()->toString();
        $name = 'name';

        $this->mockEntity = Mockery::mock(Genre::class, [
            $id,
            $name,
            true
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($id);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $this->mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepository->shouldReceive('findById')
            ->with($id)
            ->andReturn($this->mockEntity);

        $this->mockInputDto = Mockery::mock(GetInputDto::class, [
            $id
        ]);

        $useCase = new GetUseCase($this->mockRepository);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(GetUseCase::class, $useCase);
        $this->assertInstanceOf(GetOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('name', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $this->mockRepository->shouldHaveReceived('findById')->once();

        Mockery::close();
    }
}
