<?php

namespace Tests\Unit\Core\CastMember\Application\UseCase;

use Core\CastMember\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\CastMember\Application\UseCase\CreateUseCase;
use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateUseCaseUnitTest extends TestCase
{
    public function testCreate()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $name = 'name';
        $type = CastMemberType::DIRECTOR;
        $mockEntity = Mockery::mock(CastMember::class, [$uuid, $name, $type]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')
            ->once()
            ->andReturn($mockEntity);
        $useCase = new CreateUseCase($mockRepository);

        $mockDto = Mockery::mock(CreateInputDto::class, [
            $name, $type->value
        ]);

        $response = $useCase->execute($mockDto);

        $this->assertInstanceOf(CreateOutputDto::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals($name, $response->name);
        $this->assertEquals($type->value, $response->type);
        $this->assertNotEmpty($response->created_at);

        Mockery::close();
    }
}
