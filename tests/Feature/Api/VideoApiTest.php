<?php

namespace Tests\Feature\Api;

use App\Models\{
    CastMember as CastMemberModel,
    Category as CategoryModel,
    Genre as GenreModel,
    Video as VideoModel,
};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoApiTest extends TestCase
{
    protected $endpoint = '/api/videos';

    protected $serializedFields = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at',
    ];

    public function testEmpty()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertOk();
    }

    /**
     * @dataProvider providerPagination
     */
    public function testIndex(
        int $total,
        int $currentPage,
        int $page = 1,
        int $perPage = 15,
        string $filter = '',
    ) {
        VideoModel::factory($total)->create();
        if ($filter) {
            VideoModel::factory($total)->create([
                'title' => $filter
            ]);
        }
        $params = http_build_query([
            'page' => $page,
            'per_page' => $perPage,
            'order' => 'DESC',
            'filter' => $filter,
        ]);
        $response = $this->getJson("$this->endpoint?$params");
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->serializedFields,
            ],
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ],
        ]);
        $response->assertJsonCount($currentPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.per_page', $perPage);
        $response->assertJsonPath('meta.total', $total);
    }


    protected function providerPagination(): array
    {
        return [
            'test empty' => [
                'total' => 0,
                'currentPage' => 0,
                'page' => 1,
                'perPage' => 15,
            ],
            'test with total two pages' => [
                'total' => 20,
                'currentPage' => 15,
                'page' => 1,
                'perPage' => 15,
            ],
            'test page two' => [
                'total' => 20,
                'currentPage' => 5,
                'page' => 2,
                'perPage' => 15,
            ],
            'test page four' => [
                'total' => 40,
                'currentPage' => 10,
                'page' => 4,
                'perPage' => 10,
            ],
            'test with filter' => [
                'total' => 10,
                'currentPage' => 10,
                'page' => 1,
                'perPage' => 10,
                'filter' => 'test',
            ],
        ];
    }

    public function testShowNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_id");
        $response->assertNotFound();
    }

    public function testShow()
    {
        $video = VideoModel::factory()->create();

        $response = $this->getJson("$this->endpoint/{$video->id}");
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => $this->serializedFields,
        ]);
    }

    public function testStore()
    {
        $mediaVideoFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $imageVideoFile = UploadedFile::fake()->image('image.png');

        $categoriesIds = CategoryModel::factory(3)->create()->pluck('id')->toArray();
        $genresIds = GenreModel::factory(3)->create()->pluck('id')->toArray();
        $castMembersIds = CastMemberModel::factory(3)->create()->pluck('id')->toArray();

        $data = [
            'title' => 'test title',
            'description' => 'test desc',
            'year_launched' => 2000,
            'duration' => 1,
            'rating' => 'L',
            'opened' => true,
            'categories' => $categoriesIds,
            'genres' => $genresIds,
            'cast_members' => $castMembersIds,
            'thumb_file' => $imageVideoFile,
            'thumb_half_file' => $imageVideoFile,
            'banner_file' => $imageVideoFile,
            'trailer_file' => $mediaVideoFile,
            'video_file' => $mediaVideoFile,
        ];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $this->assertDatabaseHas('videos', [
            'id' => $response->json('data.id'),
        ]);

        $this->assertEquals($categoriesIds, $response->json('data.categories'));
        $this->assertEquals($genresIds, $response->json('data.genres'));
        $this->assertEquals($castMembersIds, $response->json('data.cast_members'));

        Storage::assertExists($response->json('data.video'));
        Storage::assertExists($response->json('data.trailer'));
        Storage::assertExists($response->json('data.banner'));
        Storage::assertExists($response->json('data.thumb'));
        Storage::assertExists($response->json('data.thumb_half'));

        Storage::deleteDirectory($response->json('data.id'));
    }

    public function testUpdate()
    {
        $video = VideoModel::factory()->create();

        $mediaVideoFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $imageVideoFile = UploadedFile::fake()->image('image.png');

        $categoriesIds = CategoryModel::factory(3)->create()->pluck('id')->toArray();
        $genresIds = GenreModel::factory(3)->create()->pluck('id')->toArray();
        $castMembersIds = CastMemberModel::factory(3)->create()->pluck('id')->toArray();

        $data = [
            'title' => 'title updated',
            'description' => 'desc updated',
            'categories' => $categoriesIds,
            'genres' => $genresIds,
            'cast_members' => $castMembersIds,
            'thumb_file' => $imageVideoFile,
            'thumb_half_file' => $imageVideoFile,
            'banner_file' => $imageVideoFile,
            'trailer_file' => $mediaVideoFile,
            'video_file' => $mediaVideoFile,
        ];
        $response = $this->putJson("$this->endpoint/{$video->id}", $data);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $this->assertDatabaseCount('videos', 1);
        $this->assertDatabaseHas('videos', [
            'id' => $response->json('data.id'),
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $this->assertEquals($categoriesIds, $response->json('data.categories'));
        $this->assertEquals($genresIds, $response->json('data.genres'));
        $this->assertEquals($castMembersIds, $response->json('data.cast_members'));

        Storage::assertExists($response->json('data.video'));
        Storage::assertExists($response->json('data.trailer'));
        Storage::assertExists($response->json('data.banner'));
        Storage::assertExists($response->json('data.thumb'));
        Storage::assertExists($response->json('data.thumb_half'));

        Storage::deleteDirectory($response->json('data.id'));
    }


    public function testStoreValidation()
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'title',
            'description',
            'year_launched',
            'duration',
            'rating',
            'opened',
            'categories',
            'genres',
            'cast_members',
        ]);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/fake_id");
        $response->assertNotFound();
    }

    public function testDestroy()
    {
        $video = VideoModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$video->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted($video);
    }
}
