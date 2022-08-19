<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Category\Application\UseCase\GetUseCase;
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GetUseCaseUnitTest extends TestCase
{
    public function testGetCategoryById()
    {
        $id = Uuid::uuid4()->toString();
        $name = 'name';

        $mockEntity = Mockery::mock(Category::class, [
            $id,
            $name
        ]);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->with($id)
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(GetInputDto::class, [
            $id
        ]);

        $useCase = new GetUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GetUseCase::class, $useCase);
        $this->assertInstanceOf(GetOutputDto::class, $responseUseCase);
        $this->assertEquals($id, $responseUseCase->id);
        $this->assertEquals('name', $responseUseCase->name);
        $this->assertEquals('', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);
        $mockRepository->shouldHaveReceived('findById')->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
