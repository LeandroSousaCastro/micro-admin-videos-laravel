<?php

namespace Tests\Unit\Genre\Domain\Application\UseCase;

use Core\Genre\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Genre\Application\UseCase\ListUseCase;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
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

    public function testListUseCase()
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($this->mockPagination());

        $mockDtoInput = Mockery::mock(ListInputDto::class, [
            'teste', 'desc', 1, 15
        ]);

        $useCase = new ListUseCase($mockRepository);
        $response = $useCase->execute($mockDtoInput);

        $this->assertInstanceOf(ListOutputDto::class, $response);
        $mockRepository->shouldHaveReceived()->paginate(
            'teste', 'desc', 1, 15
        );

        Mockery::close();
    }
}
