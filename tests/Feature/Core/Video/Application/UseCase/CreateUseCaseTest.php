<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\{
    CastMember,
    Category,
    Genre
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\{
    DbTransactionInterface,
};
use Core\Video\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Video\Application\UseCase\CreateUseCase;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Tests\Stubs\UploadFilesStub;
use Tests\Stubs\VideoEventStub;
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testExecuteCreate(
        int $categories,
        int $genres,
        int $castMembers,
        bool $withMediaVideo = false,
        bool $withTrailer = false,
        bool $withThumb = false,
        bool $withThumbHalf = false,
        bool $withBanner = false,
    ) {
        $useCase = new CreateUseCase(
            $this->app->make(VideoRepositoryInterface::class),
            $this->app->make(CategoryRepositoryInterface::class),
            $this->app->make(GenreRepositoryInterface::class),
            $this->app->make(CastMemberRepositoryInterface::class),
            $this->app->make(DbTransactionInterface::class),
            new UploadFilesStub(),
            new VideoEventStub()
        );

        $categoriesIds = Category::factory($categories)->create()->pluck('id')->toArray();
        $genresIds = Genre::factory($genres)->create()->pluck('id')->toArray();
        $CastMembersIds = CastMember::factory($castMembers)->create()->pluck('id')->toArray();

        $fakeFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fakeFile->getPathname(),
            'name' => $fakeFile->getFilename(),
            'type' => $fakeFile->getMimeType(),
            'error' => $fakeFile->getError(),
        ];

        $input = new CreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2020,
            duration: 120,
            opened: true,
            rating: Rating::L,
            categories: $categoriesIds,
            genres: $genresIds,
            castMembers: $CastMembersIds,
            thumbFile: $withThumb ? $file : null,
            thumbHalf: $withThumbHalf ? $file : null,
            bannerFile: $withBanner ? $file : null,
            trailerFile: $withTrailer ? $file : null,
            videoFile: $withMediaVideo ? $file : null,
        );

        $response = $useCase->execute($input);

        $this->assertInstanceOf(CreateOutputDto::class, $response);
        $this->assertEquals($input->title, $response->title);
        $this->assertEquals($input->description, $response->description);
        $this->assertEquals($input->yearLaunched, $response->yearLaunched);
        $this->assertEquals($input->duration, $response->duration);
        $this->assertEquals($input->rating, $response->rating);
        $this->assertEquals($input->title, $response->title);
        $this->assertCount($categories, $response->categories);
        $this->assertEqualsCanonicalizing($input->categories, $response->categories);
        $this->assertCount($genres, $response->genres);
        $this->assertEqualsCanonicalizing($input->genres, $response->genres);
        $this->assertCount($castMembers, $response->castMembers);
        $this->assertEqualsCanonicalizing($input->castMembers, $response->castMembers);
        $this->assertTrue($withMediaVideo ? $response->videoFile !== null : $response->videoFile === null);
        $this->assertTrue($withTrailer ? $response->trailerFile !== null : $response->trailerFile === null);
        $this->assertTrue($withBanner ? $response->bannerFile !== null : $response->bannerFile === null);
        $this->assertTrue($withThumb ? $response->thumbFile !== null : $response->thumbFile === null);
        $this->assertTrue($withThumbHalf ? $response->thumbHalf !== null : $response->thumbHalf === null);
    }

    protected function provider(): array
    {
        return [
            'Test with all IDs and media video' => [
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 3,
                'withMediaVideo' => true,
                'withTrailer' => false,
                'withThumb' => false,
                'withThumbHalf' => false,
                'withBanner' => false,
            ],
            'Test with categories and genres and without files' => [
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 0,
            ],
            'Test with all IDs and all medias' => [
                'categories' => 2,
                'genres' => 2,
                'castMembers' => 2,
                'withMediaVideo' => true,
                'withTrailer' => true,
                'withThumb' => true,
                'withThumbHalf' => true,
                'withBanner' => true,
            ],
            'Test without IDs and all medias' => [
                'categories' => 0,
                'genres' => 0,
                'castMembers' => 0,
                'withMediaVideo' => true,
                'withTrailer' => true,
                'withThumb' => true,
                'withThumbHalf' => true,
                'withBanner' => true,
            ],
        ];
    }
}