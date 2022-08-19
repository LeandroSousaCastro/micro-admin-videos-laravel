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
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->andReturn(true);

        $uuid = (string) Uuid::uuid4()->toString();
        $mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->isSuccess);
        $mockRepository->shouldHaveReceived('delete')->once();        

        Mockery::close();
    }
}
