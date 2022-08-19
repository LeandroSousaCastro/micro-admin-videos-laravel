<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Genre\Application\UseCase\UpdateUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Seedwork\Domain\ValueObject\Uuid as ValueObjectUuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateUseCaseUnitTest extends TestCase
{
    public function testUpdate()
    {
        $uuid = (string) Uuid::uuid4();

        $useCase = new UpdateUseCase($this->mockRepository($uuid), $this->mockCategoryRepository($uuid), $this->mockTransaction());
        $response = $useCase->execute($this->mockUpdateInputDto($uuid, [$uuid]));

        $this->assertInstanceOf(UpdateOutputDto::class, $response);
    }

    public function test_update_categories_notfound()
    {
        $this->expectException(NotFoundException::class);

        $uuid = (string) Uuid::uuid4();

        $useCase = new UpdateUseCase($this->mockRepository($uuid, 0), $this->mockCategoryRepository($uuid), $this->mockTransaction());
        $useCase->execute($this->mockUpdateInputDto($uuid, [$uuid, 'fake_value']));
    }

    private function mockEntity(string $uuid)
    {
        $mockEntity = Mockery::mock(Genre::class, [
            'teste', new ValueObjectUuid($uuid), [], true
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update')->times(1);
        $mockEntity->shouldReceive('addCategory');

        return $mockEntity;
    }

    private function mockRepository(string $uuid, int $timesCalled = 1)
    {
        $mockEntity = $this->mockEntity($uuid);

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')
            ->times($timesCalled)
            ->andReturn($mockEntity);

        return $mockRepository;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, DbTransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');

        return $mockTransaction;
    }

    private function mockCategoryRepository(string  $uuid)
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')
            ->once()
            ->andReturn([$uuid]);

        return $mockCategoryRepository;
    }

    private function mockUpdateInputDto(string $uuid, array $categoriesIds = [])
    {
        return Mockery::mock(UpdateInputDto::class, [
            $uuid, 'name to update', $categoriesIds
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
