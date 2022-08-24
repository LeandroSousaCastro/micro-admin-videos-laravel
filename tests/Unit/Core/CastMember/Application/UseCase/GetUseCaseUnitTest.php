<?php

namespace Tests\Unit\Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\CastMember\Application\UseCase\GetUseCase;
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class GetUseCaseUnitTest extends TestCase
{
    public function testGet()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $name = 'name';
        $type = CastMemberType::DIRECTOR;
        $mockEntity = Mockery::mock(CastMember::class, [$name, $type, $uuid]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);
        $useCase = new GetUseCase($mockRepository);

        $mockDto = Mockery::mock(GetInputDto::class, [$uuid]);

        $response = $useCase->execute($mockDto);
        $this->assertInstanceOf(GetOutputDto::class, $response);
        $this->assertEquals($uuid, $response->id);
        $this->assertEquals($name, $response->name);
        $this->assertEquals($type->value, $response->type);

        Mockery::close();
    }
}
