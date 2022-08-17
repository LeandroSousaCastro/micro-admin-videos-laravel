<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Category\Application\UseCase\UpdateUseCase;
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\RepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class UpdateUseCaseUnitTest extends TestCase
{
    public function testUpdateCategory()
    {
        $uuid = Uuid::random();
        $categoryName = 'Name';
        $categoryDesc = 'Desc';

        $this->mockEntity = Mockery::mock(Category::class, [
            $uuid, $categoryName, $categoryDesc
        ]);
        $this->mockEntity->shouldReceive('update');
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $this->mockRepository = Mockery::mock(stdClass::class, RepositoryInterface::class);
        $this->mockRepository->shouldReceive('findById')->andReturn($this->mockEntity);
        $this->mockRepository->shouldReceive('update')->andReturn($this->mockEntity);


        $this->mockInputDto = Mockery::mock(UpdateInputDto::class, [
            $uuid,
            'new name',
        ]);

        $useCase = new UpdateUseCase($this->mockRepository);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(UpdateOutputDto::class, $responseUseCase);
        $this->mockRepository->shouldHaveReceived('findById');
        $this->mockRepository->shouldHaveReceived('update');

        Mockery::close();
    }
}
