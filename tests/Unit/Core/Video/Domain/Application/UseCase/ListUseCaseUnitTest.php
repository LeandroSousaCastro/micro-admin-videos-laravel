<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

namespace Core\Video\Application\UseCase\ListUseCase;

use Core\Video\Application\Dto\{
    ListInputDto,
    ListOutputDto
};
use Core\Video\Application\UseCase\ListUseCase;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Core\Seedwork\Application\UseCase\UseCaseTrait;

class ListUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function testExecute()
    {

        $useCase = new ListUseCase(
            repository: $this->mockRepository()
        );
        $response = $useCase->execute(
            input: $this->mockInputDTO()
        );
        $this->assertInstanceOf(ListOutputDto::class, $response);
    }

    private function mockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($this->mockPagination());
        return $mockRepository;
    }

    private function mockInputDTO()
    {
        return Mockery::mock(ListInputDto::class, [
            '',
            'DESC',
            1,
            15
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
