<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

namespace Core\Video\Application\UseCase\ListUseCase;

use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Application\Dto\{
    GetInputDto,
    GetOutputDto
};
use Core\Video\Application\UseCase\GetUseCase;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUseCaseUnitTest extends TestCase
{
    protected Video $entity;

    protected function setUp(): void
    {
        $uuid = Uuid::random();
        $this->entity = new Video(
            id: $uuid,
            title: 'title',
            description: 'description',
            yearLaunched: 2022,
            duration: 120,
            opened: true,
            rating: Rating::RATE18,
        );
        parent::setUp();
    }

    public function testExecute()
    {

        $useCase = new GetUseCase(
            repository: $this->mockRepository()
        );
        $response = $useCase->execute(
            input: $this->mockInputDTO()
        );

        $this->assertInstanceOf(GetOutputDto::class, $response);
        $this->assertEquals($response->id, $this->entity->id);
        $this->assertEquals($response->title, $this->entity->title);
        $this->assertEquals($response->description, $this->entity->description);
        $this->assertEquals($response->yearLaunched, $this->entity->yearLaunched);
        $this->assertEquals($response->duration, $this->entity->duration);
        $this->assertEquals($response->opened, $this->entity->opened);
        $this->assertEquals($response->rating, $this->entity->rating);
    }

    private function mockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->once()->andReturn($this->entity);
        return $mockRepository;
    }

    private function mockInputDTO()
    {
        return Mockery::mock(GetInputDto::class, [
            $this->entity->id()
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
