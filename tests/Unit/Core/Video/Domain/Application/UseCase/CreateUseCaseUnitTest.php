<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\Video\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Video\Application\UseCase\CreateUseCase;
use Core\Video\Domain\Enum\Rating;
use Mockery;

class CreateUseCaseUnitTest extends BaseUseCaseUnitTest
{
    protected function nameActionRepository(): string
    {
        return 'insert';
    }

    protected function getUseCase(): string
    {
        return CreateUseCase::class;
    }


    protected function createMockInputDto(
        array $categoriesIds = [],
        array $genresIds = [],
        array $castMembersIds = [],
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null,
        ?array $trailerFile = null,
        ?array $videoFile = null,
    ) {
        return Mockery::mock(CreateInputDto::class, [
            'title',
            'description',
            2022,
            120,
            true,
            Rating::RATE18,
            $categoriesIds,
            $genresIds,
            $castMembersIds,
            $thumbFile,
            $thumbHalf,
            $bannerFile,
            $trailerFile,
            $videoFile,
        ]);
    }

    public function testExecuteInputOutput()
    {
        $this->createUseCase();
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );
        $this->assertInstanceOf(CreateOutputDto::class, $response);
    }
}
