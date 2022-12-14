<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use Core\Video\Application\Dto\{
    CreateInputDto
};
use Core\Video\Application\UseCase\CreateUseCase;
use Core\Video\Domain\Enum\Rating;

class CreateUseCaseTest extends BaseUseCase
{

    public function useCase(): string
    {
        return CreateUseCase::class;
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
        return new CreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2020,
            duration: 120,
            opened: true,
            rating: Rating::L,
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
