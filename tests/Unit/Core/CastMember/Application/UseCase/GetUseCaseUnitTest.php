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
use Core\Seedwork\Domain\ValueObject\Uuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class GetUseCaseUnitTest extends TestCase
{
    public function testGet()
    {
        $uuid = Uuid::random();
        $name = 'name';
        $type = CastMemberType::DIRECTOR;
        $mockEntity = Mockery::mock(CastMember::class, [$name, $type, $uuid]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($uuid->__toString())
            ->andReturn($mockEntity);
        $useCase = new GetUseCase($mockRepository);

        $mockDto = Mockery::mock(GetInputDto::class, [$uuid->__toString()]);

        $response = $useCase->execute($mockDto);
        $this->assertInstanceOf(GetOutputDto::class, $response);
        $this->assertEquals($uuid->__toString(), $response->id);
        $this->assertEquals($name, $response->name);
        $this->assertEquals($type->value, $response->type);

        Mockery::close();
    }
}
