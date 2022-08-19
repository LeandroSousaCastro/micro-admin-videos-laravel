<?php

namespace Tests\Unit\Core\Genre\Application\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Genre\Application\UseCase\CreateUseCase;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateUseCaseUnitTest extends TestCase
{
    protected $uuid;
    protected $name;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = RamseyUuid::uuid4()->toString();
        $this->name = 'name';
    }

    public function testCreateNewGenre()
    {
        $mockRepository = $this->mockRepository();
        $useCase = new CreateUseCase(
            $mockRepository,
            $this->mockCategoryRepository(),
            $this->mockTransaction()
        );
        $responseUseCase = $useCase->execute($this->mockCreateInputDto([$this->uuid]));

        $this->assertInstanceOf(CreateUseCase::class, $useCase);
        $this->assertInstanceOf(CreateOutputDto::class, $responseUseCase);
        $this->assertEquals($this->uuid, $responseUseCase->id);
        $this->assertEquals($this->name, $responseUseCase->name);
        $this->assertTrue($responseUseCase->is_active);
        $mockRepository->shouldHaveReceived('insert')->once();
    }

    public function testCreateNewGenreWithCategoriesNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category fake id not found');

        $useCase = new CreateUseCase(
            $this->mockRepository(),
            $this->mockCategoryRepository(),
            $this->mockTransaction()
        );
        $useCase->execute($this->mockCreateInputDto([$this->uuid, 'fake id']));
    }

    private function mockEntity()
    {
        $mockEntity = Mockery::mock(Genre::class, [
            $this->uuid,
            $this->name,
            [],
            true
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        return $mockEntity;
    }

    private function mockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($this->mockEntity());

        return $mockRepository;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, DbTransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollBack');

        return $mockTransaction;
    }

    private function mockCategoryRepository()
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')->once()->andReturn([$this->uuid]);

        return $mockCategoryRepository;
    }

    private function mockCreateInputDto(array $ids = [])
    {
        $mockCreateInputDto = Mockery::mock(CreateInputDto::class, [
            $this->name, $ids, true
        ]);

        return $mockCreateInputDto;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
