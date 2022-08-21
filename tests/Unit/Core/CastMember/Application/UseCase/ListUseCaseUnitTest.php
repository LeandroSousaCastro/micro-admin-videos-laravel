<?php

namespace Tests\Unit\Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\CastMember\Application\UseCase\ListUseCase;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\Core\Seedwork\Application\UseCase\UseCaseTrait;

class ListUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function testList()
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
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
