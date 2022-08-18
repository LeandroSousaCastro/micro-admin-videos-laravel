<?php

namespace Tests\Unit\Genre\Domain\Application\UseCase;

use PHPUnit\Framework\TestCase;

class GetUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetUseCase()
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($this->mockPagination());
    }
}
