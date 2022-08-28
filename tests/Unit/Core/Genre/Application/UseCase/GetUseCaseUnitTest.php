<?php

namespace Tests\Unit\Core\Genre\Application\UseCase;

use Core\Genre\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Genre\Application\UseCase\GetUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUseCaseUnitTest extends TestCase
{
    public function testGetUseCase()
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
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($id->__toString())
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(GetInputDto::class, [
            $id->__toString()
        ]);

        $useCase = new GetUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GetUseCase::class, $useCase);
        $this->assertInstanceOf(GetOutputDto::class, $responseUseCase);
        $this->assertEquals($id->__toString(), $responseUseCase->id);
        $this->assertEquals('name', $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $mockRepository->shouldHaveReceived()->findById($id->__toString());

        Mockery::close();
    }
}
