<?php

namespace Tests\Unit\Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto
};
use Core\CastMember\Application\UseCase\DeleteUseCase;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->times(1)
            ->andReturn(true);

        $uuid = (string) Uuid::uuid4()->toString();
        $mockInputDto = Mockery::mock(DeleteInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->isSuccess);

        Mockery::close();
    }

    public function testFailDelete()
    {

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->times(1)
            ->andReturn(false);

        $uuid = (string) Uuid::uuid4();
        $mockInputDto = Mockery::mock(DeleteInputDto::class, [$uuid]);
        $useCase = new DeleteUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->isSuccess);

        Mockery::close();
    }
}
