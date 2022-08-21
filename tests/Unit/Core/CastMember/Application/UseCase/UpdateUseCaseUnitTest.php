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
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class UpdateUseCaseUnitTest extends TestCase
{
    public function testUpdateCastMember()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $name = 'Name';

        $mockEntity = Mockery::mock(CastMember::class, [
            $uuid, $name, CastMemberType::DIRECTOR
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update')->once();

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')
            ->once()
            ->andReturn($mockEntity);


        $mockInputDto = Mockery::mock(UpdateInputDto::class, [
            $uuid,
            'new name',
            CastMemberType::ACTOR->value
        ]);

        $useCase = new UpdateUseCase($mockRepository);
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(UpdateOutputDto::class, $responseUseCase);

        Mockery::close();
    }
}
