<?php

namespace Tests\Unit\Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\CastMember\Application\UseCase\UpdateUseCase;
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class UpdateUseCaseUnitTest extends TestCase
{
    public function testUpdateCastMember()
    {
        $uuid = Uuid::random();
        $name = 'Name';

        $mockEntity = Mockery::mock(CastMember::class, [
            $name, CastMemberType::DIRECTOR, $uuid
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update')->once();

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($uuid->__toString())
            ->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')
            ->once()
            ->andReturn($mockEntity);


        $mockInputDto = Mockery::mock(UpdateInputDto::class, [
            'new name',
            CastMemberType::ACTOR->value,
            $uuid->__toString(),
        ]);

        $useCase = new UpdateUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(UpdateOutputDto::class, $responseUseCase);

        Mockery::close();
    }
}
