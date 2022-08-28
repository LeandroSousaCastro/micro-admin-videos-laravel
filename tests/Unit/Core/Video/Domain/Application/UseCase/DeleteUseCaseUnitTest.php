<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Application\Dto\{
    DeleteInputDto,
    DeleteOutputDto
};
use Core\Video\Application\UseCase\DeleteUseCase;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseUnitTest extends TestCase
{
    protected Video $entity;

    public function testExecute()
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
        $useCase = new DeleteUseCase(
            repository: $this->mockRepository(true)
        );
        $response = $useCase->execute($this->mockInputDTO($uuid));
        $this->assertTrue($response->isSuccess);
        $this->assertInstanceOf(DeleteOutputDto::class, $response);
    }

    private function mockRepository(bool $result)
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->once()->andReturn($result);
        return $mockRepository;
    }

    public function testFailDelete()
    {
        $uuid = (string) Uuid::random();
        $useCase = new DeleteUseCase(
            repository: $this->mockRepository(false)
        );
        $response = $useCase->execute($this->mockInputDTO($uuid));
        $this->assertFalse($response->isSuccess);
        $this->assertInstanceOf(DeleteOutputDto::class, $response);
    }

    private function mockInputDTO(string $id)
    {
        return Mockery::mock(DeleteInputDto::class, [$id]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
