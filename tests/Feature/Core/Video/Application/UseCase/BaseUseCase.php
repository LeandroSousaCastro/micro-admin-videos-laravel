<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use PHPUnit\Framework\TestCase;

abstract class BaseUseCase extends TestCase
{
    abstract function useCase(): string;

    abstract function inputDTO(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $bannerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $trailerFile = null,
        ?array $videoFile = null,
    ): object;
}
