<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\Video;
use Core\Video\Application\Dto\ChangeEncodedInputDTO;
use Core\Video\Application\UseCase\ChangeEncodedPathVideo;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Tests\TestCase;

class ChangeEncodedPathVideoTest extends TestCase
{
    public function testIfUpdatedMediaInDatabase()
    {
        $video = Video::factory()->create();

        $useCase = new ChangeEncodedPathVideo(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $input = new ChangeEncodedInputDTO(
            id: $video->id,
            encodedPath: 'path-id/video_encoded.ext',
        );

        $useCase->execute($input);

        $this->assertDatabaseHas('medias_videos', [
            'video_id' => $input->id,
            'encoded_path' => $input->encodedPath
        ]);
    }
}
