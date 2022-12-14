<?php

namespace Tests\Unit\Core\Genre\Application\UseCase;

use Core\Genre\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Genre\Application\UseCase\ListUseCase;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\Core\Seedwork\Application\UseCase\UseCaseTrait;

class ListUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

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
