<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Category\Application\UseCase\UpdateUseCase;
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
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

        $mockEntity = Mockery::mock(Category::class, [
            $uuid, $categoryName, $categoryDesc
        ]);
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntity);


        $mockInputDto = Mockery::mock(UpdateInputDto::class, [
            $uuid,
            'new name',
        ]);

        $useCase = new UpdateUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(UpdateOutputDto::class, $responseUseCase);
        $mockRepository->shouldHaveReceived('findById');
        $mockRepository->shouldHaveReceived('update');

        Mockery::close();
    }
}
