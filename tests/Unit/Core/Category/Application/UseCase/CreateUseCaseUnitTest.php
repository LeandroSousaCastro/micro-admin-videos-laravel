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

        $this->mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $name
        ]);
        $this->mockEntity->shouldReceive('constr')->andReturn($uuid);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $this->mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepository->shouldReceive('insert')->andReturn($this->mockEntity);

        $useCase = new CreateUseCase($this->mockRepository);
        $this->mockEntityCreateInputDto = Mockery::mock(CreateInputDto::class, [
            $name
        ]);
        $responseUseCase = $useCase->execute($this->mockEntityCreateInputDto);

        $this->assertInstanceOf(CreateUseCase::class, $useCase);
        $this->assertInstanceOf(CreateOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);
        $this->assertEquals($name, $responseUseCase->name);
        $this->assertEquals('', $responseUseCase->description);
        $this->assertTrue($responseUseCase->is_active);
        $this->mockRepository->shouldHaveReceived('insert')->once();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
