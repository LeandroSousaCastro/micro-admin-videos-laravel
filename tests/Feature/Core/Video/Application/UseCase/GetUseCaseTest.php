<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\Video;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Application\Dto\{
    GetInputDto,
};
use Core\Video\Application\UseCase\GetUseCase;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Tests\TestCase;

class GetUseCaseTest extends TestCase
{
    public function testExecuteGetNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('Video not found for id: fake_id');
        $useCase = new GetUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $useCase->execute(new GetInputDto(
            id: 'fake_id'
        ));
    }

    public function testExecuteGet()
    {
        $video = Video::factory()->create();
        $useCase = new GetUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $response = $useCase->execute(new GetInputDto(
            id: $video->id
        ));
        $this->assertNotNull($response);
        $this->assertEquals($video->id, $response->id);
    }
}
