<?php

namespace Test\Unit\Core\Video\Domain\Application\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Seedwork\Application\Interfaces\{
    FileStorageInterface,
    DbTransactionInterface
};
use Core\Seedwork\Domain\Exception\NotFoundException;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Domain\Entity\Video as EntityVideo;
use Core\Video\Domain\Enum\Rating;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

abstract class BaseUseCaseUnitTest extends TestCase
{
    protected $useCase;

    abstract protected function nameActionRepository(): string;

    abstract protected function getUseCase(): string;

    abstract protected function createMockInputDto(
        array $categoriesIds = [],
        array $genresIds = [],
        array $castMembersIds = [],
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null,
        ?array $trailerFile = null,
        ?array $videoFile = null,
    );

    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallMethodUpdateMediaRepository = 1,
        int $timesCallMethodCommitTransaction = 1,
        int $timesCallMethodRollbackTransaction = 0,
        int $timesCallMethodStoreFileStorage = 0,
        int $timesCallMethodDispatchEventManager = 0,
    ) {
        $this->useCase = new ($this->getUseCase())(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository,
                timesCallUpdateMedia: $timesCallMethodUpdateMediaRepository
            ),
            categoryRepository: $this->createMockRepositoryCategory(),
            genreRepository: $this->createMockRepositoryGenre(),
            castMembersRepository: $this->createMockRepositoryCastMembers(),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallMethodCommitTransaction,
                timesCallRollback: $timesCallMethodRollbackTransaction
            ),
            storage: $this->createMockFileStorage(
                timesCall: $timesCallMethodStoreFileStorage
            ),
            eventManager: $this->createMockEventManager(
                timesCall: $timesCallMethodDispatchEventManager
            ),
        );
    }

    /**
     * @dataProvider dataProviderIds
     */
    public function testValidateFilesIds(
        array $arrange,
    ) {
        $this->createUseCase(
            timesCallMethodActionRepository: 0,
            timesCallMethodUpdateMediaRepository: 0,
            timesCallMethodCommitTransaction: 0,
        );

        foreach ($arrange as $data) {
            $this->expectException(NotFoundException::class);
            $this->expectErrorMessage(sprintf(
                '%s %s not found',
                $data[0],
                implode(', ', $data[1])
            ));
            $this->useCase->execute(
                input: $this->createMockInputDto(
                    categoriesIds: $data[1]
                )
            );
        }
    }

    public function dataProviderIds(): array
    {
        return [
            'arrange' => [[
                ['Category', ['uuid-1']],
                ['Categories', ['uuid-1', 'uuid-2']],
                ['Categories', ['uuid-1', 'uuid-2', 'uuid-3', 'uuid-4']],
                ['Genre', ['uuid-1']],
                ['Genres', ['uuid-1', 'uuid-2']],
                ['Genres', ['uuid-1', 'uuid-2', 'uuid-3', 'uuid-4']],
                ['CastMember', ['uuid-1']],
                ['CastMembers', ['uuid-1', 'uuid-2']],
                ['CastMembers', ['uuid-1', 'uuid-2', 'uuid-3', 'uuid-4']],
            ]]
        ];
    }

    /**
     * @dataProvider dataProviderFiles
     */
    public function testUploadFiles(
        array $thumb,
        array $thumbHalf,
        array $banner,
        array $video,
        array $trailer,
        int $timesStorageCalls = 0,
        int $timesDispatchCalls = 0,
    ) {
        $this->createUseCase(
            timesCallMethodStoreFileStorage: $timesStorageCalls,
            timesCallMethodDispatchEventManager: $timesDispatchCalls
        );

        $response = $this->useCase->execute(
            input: $this->createMockInputDto(
                thumbFile: $thumb['value'],
                thumbHalf: $thumbHalf['value'],
                bannerFile: $banner['value'],
                trailerFile: $video['value'],
                videoFile: $trailer['value'],
            )
        );
        $this->assertEquals($response->thumbFile, $thumb['expected']);
        $this->assertEquals($response->thumbHalf, $thumbHalf['expected']);
        $this->assertEquals($response->bannerFile, $banner['expected']);
        $this->assertEquals($response->trailerFile, $video['expected']);
        $this->assertEquals($response->videoFile, $trailer['expected']);
    }

    public function dataProviderFiles(): array
    {
        $expected = 'path/file.png';
        return [
            [
                'thumb' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expected' => $expected],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/thumbHalf.png'], 'expected' => $expected],
                'banner' => ['value' => ['tmp' => 'tmp/banner.png'], 'expected' => $expected],
                'trailer' => ['value' => ['tmp' => 'tmp/trailer.mp4'], 'expected' => $expected],
                'video' => ['value' => ['tmp' => 'tmp/video.mp4'], 'expected' => $expected],
                'timesStorageCalls' => 5,
                'timesDispatchCalls' => 1
            ], [
                'thumb' => ['value' => null, 'expected' => null],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/thumbHalf.png'], 'expected' => $expected],
                'banner' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => ['tmp' => 'tmp/trailer.mp4'], 'expected' => $expected],
                'video' => ['value' => null, 'expected' => null],
                'timesStorageCalls' => 2,
            ], [
                'thumb' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expected' => $expected],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/thumbHalf.png'], 'expected' => $expected],
                'banner' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => ['tmp' => 'tmp/trailer.mp4'], 'expected' => $expected],
                'video' => ['value' => null, 'expected' => null],
                'timesStorageCalls' => 3,
            ], [
                'thumb' => ['value' => null, 'expected' => null],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'video' => ['value' => null, 'expected' => null],
            ]
        ];
    }

    private function createMockRepository(
        int $timesCallAction,
        int $timesCallUpdateMedia
    ) {
        $entity = $this->createEntity();
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive($this->nameActionRepository())
            ->times($timesCallAction)
            ->andReturn($entity);
        $mockRepository->shouldReceive('findById')->andReturn($entity);
        $mockRepository->shouldReceive('updateMedia')->times($timesCallUpdateMedia);
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

    private function createMockTransaction(
        int $timesCallCommit,
        int $timesCallRollback,
    ) {
        $mockTransaction = Mockery::mock(stdClass::class, DbTransactionInterface::class);
        $mockTransaction->shouldReceive('commit')->times($timesCallCommit);
        $mockTransaction->shouldReceive('rollback')->times($timesCallRollback);
        return $mockTransaction;
    }

    private function createMockFileStorage(int $timesCall)
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')->times($timesCall)->andReturn('path/file.png');
        return $mockFileStorage;
    }

    private function createMockEventManager(int $timesCall)
    {
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch')->times($timesCall);
        return $mockEventManager;
    }

    private function createEntity()
    {
        return new EntityVideo(
            title: 'title',
            description: 'description',
            yearLaunched: 2022,
            duration: 120,
            opened: true,
            rating: Rating::RATE18,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
