<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Enums\ImageTypes;
use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as ModelVideo;
use Core\Seedwork\Domain\Exception\NotFoundException;
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Video\Domain\Entity\Video as EntityVideo;
use Core\Seedwork\Domain\ValueObject\Uuid;
use Core\Video\Domain\Enum\MediaStatus;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Tests\TestCase;

class VideoEloquentRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoEloquentRepository(new ModelVideo());
    }

    public function testImplementInterface()
    {
        $this->assertInstanceOf(
            VideoRepositoryInterface::class,
            $this->repository
        );
    }

    public function testInsert()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
        );
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(EntityVideo::class, $response);
        $this->assertDatabaseHas('videos', [
            'id' => $entity->id()
        ]);
    }

    public function testInsertWithRelationships()
    {
        $categories = Category::factory(4)->create();
        $genres = Genre::factory(4)->create();
        $castMembers = CastMember::factory(4)->create();

        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
        );

        foreach ($categories as $category) {
            $entity->addCategoryId($category->id);
        }
        foreach ($genres as $genre) {
            $entity->addGenreId($genre->id);
        }
        foreach ($castMembers as $castMember) {
            $entity->addCastMemberId($castMember->id);
        }

        $response = $this->repository->insert($entity);

        $this->assertDatabaseHas('videos', [
            'id' => $entity->id()
        ]);

        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertDatabaseCount('cast_member_video', 4);

        $this->assertEquals($categories->pluck('id')->toArray(), $response->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $response->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $response->castMembersId);
    }

    public function testNotFoundVideo()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage("Video not found for id: fake id");
        $this->repository->findById('fake id');
    }

    public function testFinById()
    {
        $video = ModelVideo::factory()->create();
        $response = $this->repository->findById($video->id);
        $this->assertEquals($video->id, $response->id());
        $this->assertEquals($video->title, $response->title);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    public function testFindAll()
    {
        ModelVideo::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
    }

    public function testFindAllWithFilter()
    {
        ModelVideo::factory()->count(10)->create();
        ModelVideo::factory()->count(10)->create([
            'title' => 'Test',
        ]);

        $response = $this->repository->findAll(
            filter: 'Test'
        );

        $this->assertCount(10, $response);
        $this->assertDatabaseCount('videos', 20);
    }

    /**
     * @dataProvider dataProviderPagination
     */
    public function testPagination(
        int $page,
        int $totalPage,
        int $total = 50,
    ) {
        ModelVideo::factory()->count($total)->create();

        $response = $this->repository->paginate(
            page: $page,
            totalPage: $totalPage
        );

        $this->assertCount($totalPage, $response->items());
        $this->assertEquals($total, $response->total());
        $this->assertEquals($page, $response->currentPage());
        $this->assertEquals($totalPage, $response->perPage());
    }

    public function dataProviderPagination(): array
    {
        return [
            [
                'page' => 1,
                'totalPage' => 10,
                'total' => 100,
            ], [
                'page' => 2,
                'totalPage' => 15,
            ], [
                'page' => 3,
                'totalPage' => 15,
            ],
        ];
    }

    public function testUpdateNotFoundId()
    {
        $this->expectException(NotFoundException::class);
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
        );
        $this->expectErrorMessage("Video not found for id: {$entity->id()}");
        $this->repository->update($entity);
    }

    public function testUpdate()
    {
        $categories = Category::factory()->count(10)->create();
        $genres = Genre::factory()->count(10)->create();
        $castMembers = CastMember::factory()->count(10)->create();

        $videoModel = ModelVideo::factory()->create();

        $this->assertDatabaseHas('videos', [
            'title' => $videoModel->title,
        ]);

        $entity = new EntityVideo(
            id: new Uuid($videoModel->id),
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            createdAt: new \DateTime($videoModel->created_at),
        );

        foreach ($categories as $category) {
            $entity->addCategoryId($category->id);
        }
        foreach ($genres as $genre) {
            $entity->addGenreId($genre->id);
        }
        foreach ($castMembers as $castMember) {
            $entity->addCastMemberId($castMember->id);
        }

        $response = $this->repository->update($entity);

        $this->assertDatabaseHas('videos', [
            'title' => 'Test',
        ]);

        $this->assertDatabaseCount('category_video', 10);
        $this->assertDatabaseCount('genre_video', 10);
        $this->assertDatabaseCount('cast_member_video', 10);

        $this->assertEquals($categories->pluck('id')->toArray(), $response->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $response->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $response->castMembersId);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage("Video not found for id: fake id");
        $this->repository->delete('fake id');
    }

    public function testDelete()
    {
        $video = ModelVideo::factory()->create();
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
        ]);
        $response = $this->repository->delete($video->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted($video);
    }

    public function testInsertWithMediaVideo()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            videoFile: new Media(
                filePath: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING,
            ),
        );
        $this->repository->insert($entity);

        $this->assertDatabaseCount('medias_videos', 0);
        $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('medias_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test.mp4',
            'media_status' => MediaStatus::PROCESSING->value,
        ]);

        $entity->setVideoFile(new Media(
            filePath: 'test2.mp4',
            mediaStatus: MediaStatus::COMPLETE,
            encodedPath: 'test2.encoded',
        ));

        $entityModel = $this->repository->updateMedia($entity);
        $this->assertDatabaseCount('medias_videos', 1);
        $this->assertDatabaseHas('medias_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test2.mp4',
            'media_status' => MediaStatus::COMPLETE->value,
            'encoded_path' => 'test2.encoded',
        ]);

        $this->assertNotNull($entityModel->videoFile());
    }

    public function testInsertWithMediaTrailer()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            trailerFile: new Media(
                filePath: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING,
            ),
        );
        $this->repository->insert($entity);

        $this->assertDatabaseCount('medias_videos', 0);
        $this->repository->updateMedia($entity);
        $this->repository->updateMedia($entity);
        $this->repository->updateMedia($entity);
        $this->assertDatabaseCount('medias_videos', 1);
        $this->assertDatabaseHas('medias_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test.mp4',
            'media_status' => MediaStatus::PROCESSING->value,
        ]);

        $entity->setTrailerFile(new Media(
            filePath: 'test2.mp4',
            mediaStatus: MediaStatus::COMPLETE,
            encodedPath: 'test2.encoded.mp4'
        ));
        $videoModel = $this->repository->updateMedia($entity);
        $this->assertDatabaseCount('medias_videos', 1);
        $this->assertDatabaseHas('medias_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test2.mp4',
            'media_status' => MediaStatus::COMPLETE->value,
            'encoded_path' => 'test2.encoded.mp4'
        ]);

        $this->assertNotNull($videoModel->trailerFile());
    }

    public function testInsertWithImageThumb()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            thumbFile: new Image(
                path: 'test.jpg',
            ),
        );
        $this->repository->insert($entity);
        $this->assertDatabaseCount('images_videos', 0);

        $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test.jpg',
            'type' => ImageTypes::THUMB->value,
        ]);

        $entity->setThumbFile(new Image(
            path: 'test2.jpg',
        ));
        $entityModel = $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test2.jpg',
            'type' => ImageTypes::THUMB->value,
        ]);
        $this->assertDatabaseCount('images_videos', 1);

        $this->assertNotNull($entityModel->thumbFile());
    }

    public function testInsertWithImageThumbHalf()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            thumbHalf: new Image(
                path: 'test.jpg',
            ),
        );
        $this->repository->insert($entity);
        $this->assertDatabaseCount('images_videos', 0);

        $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test.jpg',
            'type' => ImageTypes::THUMB_HALF->value,
        ]);

        $entity->setThumbHalf(new Image(
            path: 'test2.jpg',
        ));
        $entityModal = $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test2.jpg',
            'type' => ImageTypes::THUMB_HALF->value,
        ]);
        $this->assertDatabaseCount('images_videos', 1);

        $this->assertNotNull($entityModal->thumbHalf());
    }

    public function testInsertWithImageBanner()
    {
        $entity = new EntityVideo(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 1,
            opened: true,
            bannerFile: new Image(
                path: 'test.jpg',
            ),
        );
        $this->repository->insert($entity);
        $this->assertDatabaseCount('images_videos', 0);

        $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test.jpg',
            'type' => ImageTypes::BANNER->value,
        ]);

        $entity->setBannerFile(new Image(
            path: 'test2.jpg',
        ));
        $entityModel = $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('images_videos', [
            'video_id' => $entity->id(),
            'path' => 'test2.jpg',
            'type' => ImageTypes::BANNER->value,
        ]);
        $this->assertDatabaseCount('images_videos', 1);

        $this->assertNotNull($entityModel->bannerFile());
    }
}
