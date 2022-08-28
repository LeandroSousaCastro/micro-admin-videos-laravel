<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\Video;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Application\Dto\{
    DeleteInputDto,
};
use Core\Video\Application\UseCase\DeleteUseCase;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExecuteDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('Video not found for id: fake_id');
        $useCase = new DeleteUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $useCase->execute(new DeleteInputDto(
            id: 'fake_id'
        ));
    }

    public function testExecuteDelete()
    {
        $video = Video::factory()->create();
        $useCase = new DeleteUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $response = $useCase->execute(new DeleteInputDto(
            id: $video->id
        ));
        $this->assertTrue($response->isSuccess);
    }
}
