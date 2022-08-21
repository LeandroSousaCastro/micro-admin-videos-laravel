<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\{
    StoreCastMember,
    UpdateCastMember,
};
use App\Http\Controllers\Api\CastMemberController;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\CastMember\Application\UseCase\{
    ListUseCase,
    GetUseCase,
    CreateUseCase,
    UpdateUseCase,
    DeleteUseCase
};
use App\Models\CastMember as ModelCastMember;
use Core\CastMember\Domain\Enum\CastMemberType;
use Core\Seedwork\Domain\Exception\NotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    const CONTENT_TYPE = 'application/json';

    protected $repository;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new ModelCastMember());
        $this->controller = new CastMemberController();
    }

    public function testIndex()
    {
        $useCase = new ListUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testShow()
    {
        $castMember = ModelCastMember::factory()->create();

        $response = $this->controller->show(
            useCase: new GetUseCase($this->repository),
            id: $castMember->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testStore()
    {
        $useCase = new CreateUseCase($this->repository);
        $request = new StoreCastMember();
        $request->headers->set('content-type', self::CONTENT_TYPE);
        $name = 'CastMember Name';
        $type = CastMemberType::ACTOR->value;
        $request->setJson(new ParameterBag([
            'name' => $name,
            'type' => $type,
        ]));
        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertDatabaseHas('cast_members', [
            'name' => $name,
            'type' => $type,
        ]);
    }

    public function testUpdateNoFoundEntity()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('CastMember not found');
        $request = new UpdateCastMember();
        $request->headers->set('content-type', self::CONTENT_TYPE);
        $request->setJson(new ParameterBag([
            'name' => 'CastMember name',
            'type' => 1,
        ]));

        $this->controller->update(
            request: $request,
            useCase: new UpdateUseCase($this->repository),
            id: 'invalid-id'
        );
    }

    public function testUpdate()
    {
        $castMember = ModelCastMember::factory()->create();

        $request = new UpdateCastMember();
        $request->headers->set('content-type', self::CONTENT_TYPE);
        $request->setJson(new ParameterBag([
            'name' => 'CastMember name',
            'type' => 1,
        ]));

        $response = $this->controller->update(
            request: $request,
            useCase: new UpdateUseCase($this->repository),
            id: $castMember->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertDatabaseHas('cast_members', [
            'name' => 'CastMember name',
            'type' => 1,
        ]);
    }

    public function testDeleteNotFoundEntity()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('CastMember not found');
        $this->controller->destroy(
            useCase: new DeleteUseCase($this->repository),
            id: 'invalid-id'
        );
    }

    public function testDelete()
    {
        $castMember = ModelCastMember::factory()->create();

        $response = $this->controller->destroy(
            useCase: new DeleteUseCase($this->repository),
            id: $castMember->id
        );
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertSoftDeleted('cast_members', [
            'id' => $castMember->id,
        ]);
    }
}
