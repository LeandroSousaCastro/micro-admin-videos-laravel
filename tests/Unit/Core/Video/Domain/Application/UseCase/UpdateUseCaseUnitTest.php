<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Application\Dto\{
    UpdateInputDto,
    UpdateOutputDto
};
use Core\Video\Application\UseCase\UpdateUseCase;
use Core\Video\Domain\Enum\Rating;
use Mockery;

class UpdateUseCaseUnitTest extends BaseUseCaseUnitTest
{
    protected function nameActionRepository(): string
    {
        return 'update';
    }

    protected function getUseCase(): string
    {
        return UpdateUseCase::class;
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
        return Mockery::mock(UpdateInputDto::class, [
            Uuid::random(),
            'title',
            'description',
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
        $this->assertInstanceOf(UpdateOutputDto::class, $response);
    }
}
