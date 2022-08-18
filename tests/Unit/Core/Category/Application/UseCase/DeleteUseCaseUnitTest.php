<?php

namespace Tests\Unit\Core\Category\Application\UseCase;

use Core\Category\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto
};
use Core\Category\Application\UseCase\DeleteUseCase;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteUseCaseUnitTest extends TestCase
{
    public function testUpdateCategory()
    {
        $this->mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepository->shouldReceive('delete')->andReturn(true);

        $uuid = (string) Uuid::uuid4()->toString();
        $this->mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteUseCase($this->mockRepository);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->isSuccess);

        /**
         * Spies
         */
        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive('delete')->andReturn(true);
        $useCase = new DeleteUseCase($this->spy);
        $useCase->execute($this->mockInputDto);
        $this->mockRepository->shouldHaveReceived('delete')->once();

        Mockery::close();
    }
}
