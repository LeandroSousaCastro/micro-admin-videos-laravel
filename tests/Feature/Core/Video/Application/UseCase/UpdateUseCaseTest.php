<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\Video;
use Core\Video\Application\Dto\{
    UpdateInputDto
};
use Core\Video\Application\UseCase\UpdateUseCase;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Support\Facades\Event;

class UpdateUseCaseTest extends BaseUseCase
{

    public function useCase(): string
    {
        return UpdateUseCase::class;
    }

    public function inputDTO(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null,
        ?array $trailerFile = null,
        ?array $videoFile = null,
    ): object {
        $video = Video::factory()->create();
        return new UpdateInputDto(
            id: $video->id,
            title: 'test',
            description: 'test',
            categories: $categories,
            genres: $genres,
            castMembers: $castMembers,
            thumbFile: $thumbFile,
            thumbHalf: $thumbHalf,
            bannerFile: $bannerFile,
            trailerFile: $trailerFile,
            videoFile: $videoFile,
        );
    }
}
