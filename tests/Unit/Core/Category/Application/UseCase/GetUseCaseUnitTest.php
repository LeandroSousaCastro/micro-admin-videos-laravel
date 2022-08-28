<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Category\Application\UseCase\GetUseCase;
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUseCaseUnitTest extends TestCase
{
    public function testGetCategoryById()
    {
        $id = Uuid::random();
        $name = 'name';
        $description = 'teste description';
        $isActive = false;

        $mockEntity = Mockery::mock(Category::class, [
            $name,
            $description,
            $isActive,
            $id
        ]);
        $mockEntity->shouldReceive('id')->andReturn($id);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
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
        $this->assertEquals($description, $responseUseCase->description);
        $this->assertFalse($responseUseCase->is_active);
        $mockRepository->shouldHaveReceived('findById')->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
