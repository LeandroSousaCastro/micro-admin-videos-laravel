<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Application\Dto\{
    CreateInputDto,
    CreateOutputDto
};
use Core\Seedwork\Application\Interfaces\{
    FileStorageInterface,
    DbTransactionInterface
};
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Application\UseCase\CreateUseCase;
use Core\Video\Domain\Entity\Video as EntityVideo;
use Core\Video\Domain\Enum\Rating;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateUseCaseUnitTest extends TestCase
{
    protected $useCase;

    protected function setUp(): void
    {
        $this->useCase = new CreateUseCase(
            repository: $this->createMockRepository(),
            categoryRepository: $this->createMockRepositoryCategory(),
            genreRepository: $this->createMockRepositoryGenre(),
            castMembersRepository: $this->createMockRepositoryCastMembers(),
            transaction: $this->createMockTransaction(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
        );

        parent::setUp();
    }

    public function testExecuteInputOutput()
    {
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );
        $this->assertInstanceOf(CreateOutputDto::class, $response);
    }

    /**
     * @dataProvider dataProviderIds
     */
    public function testValidateCategoriesIds(
        string $label,
        array $ids,
    ) {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage(sprintf(
            '%s %s not found',
            $label,
            implode(', ', $ids)
        ));
        $this->useCase->execute(
            input: $this->createMockInputDto(
                categoriesIds: $ids
            )
        );
    }

    public function dataProviderIds(): array
    {
        return [
            ['Category', ['uuid-1']],
            ['Categories', ['uuid-1', 'uuid-2']],
            ['Categories', ['uuid-1', 'uuid-2', 'uuid-3', 'uuid-4']],
        ];
    }

    public function testUploadFiles()
    {
        $response = $this->useCase->execute(
            input: $this->createMockInputDto(
                thumbFile: ['tmp' => 'tmp/thumbFile.png'],
                thumbHalf: ['tmp' => 'tmp/thumbHalf.png'],
                bannerFile: ['tmp' => 'tmp/bannerFile.png'],
                trailerFile: ['tmp' => 'tmp/trailerFile.mp4'],
                videoFile: ['tmp' => 'tmp/videoFile.mp4']
            )
        );
        $this->assertNotNull($response->thumbFile);
        $this->assertNotNull($response->thumbHalf);
        $this->assertNotNull($response->bannerFile);
        $this->assertNotNull($response->trailerFile);
        $this->assertNotNull($response->videoFile);
    }

    private function createMockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($this->createMockEntity());
        $mockRepository->shouldReceive('updateMedia');
        return $mockRepository;
    }

    private function createMockRepositoryCategory(array $categoriesResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($categoriesResponse);
        return $mockRepository;
    }

    private function createMockRepositoryGenre(array $genresResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($genresResponse);
        return $mockRepository;
    }

    private function createMockRepositoryCastMembers(array $castMembersResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($castMembersResponse);
        return $mockRepository;
    }

    private function createMockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, DbTransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');
        return $mockTransaction;
    }

    private function createMockFileStorage()
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')->andReturn('path/file.png');
        return $mockFileStorage;
    }

    private function createMockEventManager()
    {
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch');
        return $mockEventManager;
    }

    private function createMockInputDto(
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

    private function createMockEntity()
    {
        return Mockery::mock(EntityVideo::class, [
            'title',
            'description',
            2022,
            120,
            true,
            Rating::RATE18,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
