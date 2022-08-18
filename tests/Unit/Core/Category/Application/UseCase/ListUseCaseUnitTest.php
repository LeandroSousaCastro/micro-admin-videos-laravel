<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Category\Application\UseCase\ListUseCase;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Seedwork\Domain\Repository\PaginationInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListUseCaseUnitTest extends TestCase
{
    protected function mockPagination(array $items = [])
    {
        $this->mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $this->mockPagination->shouldReceive('items')->andReturn($items);
        $this->mockPagination->shouldReceive('total')->andReturn(0);
        $this->mockPagination->shouldReceive('lastPage')->andReturn(0);
        $this->mockPagination->shouldReceive('firstPage')->andReturn(0);
        $this->mockPagination->shouldReceive('currentPage')->andReturn(0);
        $this->mockPagination->shouldReceive('perPage')->andReturn(0);
        $this->mockPagination->shouldReceive('to')->andReturn(0);
        $this->mockPagination->shouldReceive('from')->andReturn(0);

        return $this->mockPagination;
    }

    public function testListCategoriesEmpty()
    {
        $mockPagination = $this->mockPagination();

        $this->mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepository->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListUseCase($this->mockRepository);
        $this->mockListInputDto = Mockery::mock(ListInputDto::class, [
            'filter',
            'order'
        ]);
        $responseUseCase = $useCase->execute($this->mockListInputDto);

        $this->assertInstanceOf(ListUseCase::class, $useCase);
        $this->assertInstanceOf(ListOutputDto::class, $responseUseCase);
        $this->assertCount(0, $responseUseCase->items);
        $this->mockRepository->shouldHaveReceived('paginate')->once();
    }

    public function testListCategories()
    {
        $register = new stdClass();
        $register->id = 'id';
        $register->name = 'name';
        $register->description = 'description';
        $register->is_active = true;
        $register->created_at = 'created_at';
        $register->updated_at = 'updated_at';
        $register->deleted_at = 'deleted_at';

        $mockPagination = $this->mockPagination([
            $register,
        ]);

        $this->mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepository->shouldReceive('paginate')->andReturn($mockPagination);
        $useCase = new ListUseCase($this->mockRepository);
        $this->mockListInputDto = Mockery::mock(ListInputDto::class, [
            'filter',
            'order'
        ]);
        $responseUseCase = $useCase->execute($this->mockListInputDto);

        $this->assertInstanceOf(ListUseCase::class, $useCase);
        $this->assertInstanceOf(ListOutputDto::class, $responseUseCase);
        $this->assertCount(1, $responseUseCase->items);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
