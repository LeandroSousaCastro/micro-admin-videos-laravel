<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
use Illuminate\Http\Response;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    protected $endpoint = '/api/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllCategories()
    {
        ModelCategory::factory(30)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonStructure([
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

    public function testListPaginateAllCategories()
    {
        ModelCategory::factory(25)->create();
        $page = 2;
        $response = $this->getJson("$this->endpoint?page=$page");
        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => 25]);
        $this->assertEquals($page, $response->json('meta.current_page'));
        $response->assertJsonCount(10, 'data');
    }

    public function testCategoryNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCategoryFindById()
    {
        $category = ModelCategory::factory()->create();
        $response = $this->getJson("$this->endpoint/$category->id");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->is_active
        ]);
    }

    public function testCategoryValidationStore()
    {
        $data = [];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
    }

    public function testCategoryStore()
    {
        $category = ModelCategory::factory()->make();
        $response = $this->postJson($this->endpoint, $category->toArray());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->is_active
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $response->json('data.id'),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->is_active
        ]);

        $response = $this->postJson($this->endpoint, [
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => false
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => false
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $response->json('data.id'),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => false
        ]);

        $response = $this->postJson($this->endpoint, [
            'name' => $category->name,
            'description' => '',
            'is_active' => $category->is_active
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => $category->name,
            'description' => '',
            'is_active' => $category->is_active
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $response->json('data.id'),
            'name' => $category->name,
            'description' => '',
            'is_active' => $category->is_active
        ]);
    }

    public function testCategoryNotFoundUpdate()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->putJson("$this->endpoint/fake_id", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCategoryValidationUpdate()
    {
        $category = ModelCategory::factory()->create();
        $data = [];
        $response = $this->putJson("$this->endpoint/$category->id", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
    }

    public function testCategoryUpdate()
    {
        $category = ModelCategory::factory()->create();
        $data = [
            'name' => 'test',
        ];
        $response = $this->putJson("$this->endpoint/$category->id", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $category->id,
            'name' => $data['name'],
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $data['name'],
        ]);
    }

    public function testCategoryNotFoundDelete()
    {
        $response = $this->deleteJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCategoryDelete()
    {
        $category = ModelCategory::factory()->create();
        $response = $this->deleteJson("$this->endpoint/$category->id");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }
}
