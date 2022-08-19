<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Category\Application\UseCase\CreateUseCase;
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateUseCaseUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {
        $uuid = Uuid::random();
        $name = 'name';

        $mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $name
        ]);
        $mockEntity->shouldReceive('constr')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($mockEntity);

        $useCase = new CreateUseCase($mockRepository);
        $mockEntityCreateInputDto = Mockery::mock(CreateInputDto::class, [
            $name
        ]);
        $responseUseCase = $useCase->execute($mockEntityCreateInputDto);

        $this->assertInstanceOf(CreateUseCase::class, $useCase);
        $this->assertInstanceOf(CreateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals($name, $responseUseCase->name);
        $this->assertEquals('', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);
        $mockRepository->shouldHaveReceived('insert')->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
