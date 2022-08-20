<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
use App\Models\Genre as ModelGenre;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function testIndexEmpty()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testIndex()
    {
        ModelGenre::factory(30)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'is_active',
                    'created_at',
                ],
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
        $response->assertJsonCount(15, 'data');
    }

    public function testIndexPaginate()
    {
        ModelGenre::factory(25)->create();
        $page = 2;
        $response = $this->getJson("$this->endpoint?page=$page");
        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => 25]);
        $this->assertEquals($page, $response->json('meta.current_page'));
        $response->assertJsonCount(10, 'data');
    }

    public function testShowNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow()
    {
        $genre = ModelGenre::factory()->create();
        $response = $this->getJson("$this->endpoint/$genre->id");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $genre->id,
            'name' => $genre->name,
            'is_active' => $genre->is_active
        ]);
    }

    public function testStore()
    {
        $categories = ModelCategory::factory()->count(10)->create();
        $response = $this->postJson($this->endpoint, [
            'name' => 'test',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray(),
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function testStoreValidation()
    {
        $categories = ModelCategory::factory()->count(10)->create();
        $payload = [
            'name' => '',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray(),
        ];
        $response = $this->postJson($this->endpoint, $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function testNotFoundUpdate()
    {

        $categories = ModelCategory::factory()->count(10)->create();
        $payload = [
            'name' => 'Genre',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray(),
        ];
        $response = $this->putJson("$this->endpoint/fake_id", $payload);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testValidationUpdate()
    {
        $genre = ModelGenre::factory()->create();
        $data = [];
        $response = $this->putJson("$this->endpoint/$genre->id", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'categories_ids'
            ]
        ]);
    }

    public function testUpdate()
    {
        $genre = ModelGenre::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $payload = [
            'name' => 'Genre',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray(),
        ];
        $response = $this->putJson("$this->endpoint/$genre->id", $payload);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $genre->id,
            'name' => $payload['name'],
        ]);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $payload['name'],
        ]);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDestroy()
    {
        $genre = ModelGenre::factory()->create();
        $response = $this->deleteJson("$this->endpoint/$genre->id");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('Genres', [
            'id' => $genre->id,
        ]);
    }
}
