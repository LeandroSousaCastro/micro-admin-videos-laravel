<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Application\Dto\{
    ChangeEncodedInputDTO,
    ChangeEncodedOutputDTO
};
use Core\Video\Application\UseCase\ChangeEncodedPathVideo;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class ChangeEncodedPathVideoUnitTest extends TestCase
{
    public function testSpies()
    {
        $input = new ChangeEncodedInputDTO(
            id: 'id-video',
            encodedPath: 'path/video_encoded.ext'
        );
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($input->id)
            ->andReturn($this->getEntity());
        $mockRepository->shouldReceive('updateMedia')
            ->once();

        $useCase = new ChangeEncodedPathVideo(
            repository: $mockRepository
        );

        $response = $useCase->execute(input: $input);

        $this->assertInstanceOf(ChangeEncodedOutputDTO::class, $response);

        Mockery::close();
    }

    public function testExceptionRepository()
    {
        $this->expectException(NotFoundException::class);

        $input = new ChangeEncodedInputDTO(
            id: 'id-video',
            encodedPath: 'path/video_encoded.ext',
        );

        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->times(1)
            ->with($input->id)
            ->andThrow(new NotFoundException('Not Found Video'));
        $mockRepository->shouldReceive('updateMedia')
            ->times(0);

        $useCase = new ChangeEncodedPathVideo(
            repository: $mockRepository
        );

        $useCase->execute(input: $input);

        Mockery::close();
    }


    private function getEntity()
    {
        return new Video(
            title: 'title',
            description: 'description',
            yearLaunched: 2022,
            duration: 120,
            opened: true,
            rating: Rating::RATE18,
        );
    }
}
