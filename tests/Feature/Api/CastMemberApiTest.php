<?php

namespace Tests\Feature\Api;

use App\Models\CastMember as ModelCastMember;
use Illuminate\Http\Response;
use Tests\TestCase;

class CastMemberApiTest extends TestCase
{
    protected $endpoint = '/api/cast_members';

    public function testListEmpty()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAll()
    {
        ModelCastMember::factory()->count(50)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
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

    public function testListAllPageTwo()
    {
        ModelCastMember::factory()->count(20)->create();
        $response = $this->getJson("$this->endpoint?page=2");
        $response->assertStatus(Response::HTTP_OK);
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
        $response->assertJsonCount(5, 'data');
        $response->assertJsonFragment(['current_page' => 2]);
        $response->assertJsonFragment(['total' => 20]);
        $response->assertJsonFragment(['per_page' => 15]);
    }

    public function testListPaginateAll()
    {
        ModelCastMember::factory(25)->create();
        $page = 2;
        $response = $this->getJson("$this->endpoint?page=$page");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['total' => 25]);
        $this->assertEquals($page, $response->json('meta.current_page'));
        $response->assertJsonCount(10, 'data');
    }

    public function testListPaginateFilters()
    {
        ModelCastMember::factory()->count(10)->create();
        ModelCastMember::factory()->count(10)->create([
            'name' => 'Teste',
        ]);
        $response = $this->getJson("$this->endpoint?filter=Teste");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10, 'data');
    }

    public function testShowNotFound()
    {
        $response = $this->getJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow()
    {
        $castMember = ModelCastMember::factory()->create();
        $response = $this->getJson("$this->endpoint/$castMember->id");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $castMember->id,
            'name' => $castMember->name,
            'type' => $castMember->type,
            'created_at' => $castMember->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function testValidationStore()
    {
        $response = $this->postJson($this->endpoint, []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name', 'type']);
    }

    public function testStore()
    {
        $castMember = ModelCastMember::factory()->make();
        $response = $this->postJson($this->endpoint, $castMember->toArray());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $castMember->name,
            'type' => $castMember->type
        ]);
        $this->assertDatabaseHas('cast_members', [
            'id' => $response->json('data.id'),
            'name' => $castMember->name,
            'type' => $castMember->type,
        ]);
    }

    public function testUpdateNotFound()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->putJson("$this->endpoint/fake_id", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateValidation()
    {
        $castMember = ModelCastMember::factory()->create();
        $data = [];
        $response = $this->putJson("$this->endpoint/$castMember->id", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
    }

    public function testUpdateWithOutType()
    {
        $castMember = ModelCastMember::factory()->create();
        $data = [
            'name' => 'test',
        ];
        $response = $this->putJson("$this->endpoint/$castMember->id", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $castMember->id,
            'name' => $data['name'],
            'type' => $castMember->type,
        ]);
        $this->assertDatabaseHas('cast_members', [
            'id' => $castMember->id,
            'name' => $data['name'],
            'type' => $castMember->type,
        ]);
    }

    public function testUpdateWithType()
    {
        $castMember = ModelCastMember::factory()->create([
            'type' => 2
        ]);
        $data = [
            'name' => 'test',
            'type' => 1,
        ];
        $response = $this->putJson("$this->endpoint/$castMember->id", $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ]
        ]);
        $response->assertJsonFragment([
            'id' => $castMember->id,
            'name' => $data['name'],
            'type' => $data['type'],
        ]);
        $this->assertDatabaseHas('cast_members', [
            'id' => $castMember->id,
            'name' => $data['name'],
            'type' => $data['type'],
        ]);
    }

    public function testNotFoundDelete()
    {
        $response = $this->deleteJson("$this->endpoint/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDelete()
    {
        $castMember = ModelCastMember::factory()->create();
        $response = $this->deleteJson("$this->endpoint/$castMember->id");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('cast_members', [
            'id' => $castMember->id,
        ]);
    }
}
