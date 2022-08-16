<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Requests\{
    StoreCategoryRequest,
    UpdateCategoryRequest
};
use App\Http\Controllers\Api\CategoryController;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Application\UseCase\ListUseCase as CategoryListUseCase;
use Core\Category\Application\UseCase\GetUseCase as CategoryGetUseCase;
use Core\Category\Application\UseCase\CreateUseCase as CategoryCreateUseCase;
use Core\Category\Application\UseCase\UpdateUseCase as CategoryUpdateUseCase;
use Core\Category\Application\UseCase\DeleteUseCase as CategoryDeleteUseCase;
use App\Models\Category as ModelCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerUnitTest extends TestCase
{
    protected $repository;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new ModelCategory());
        $this->controller = new CategoryController();
    }

    public function testIndex()
    {
        $useCase = new CategoryListUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testShow() 
    {
        $category = ModelCategory::factory()->create();

        $response = $this->controller->show(
            useCase: new CategoryGetUseCase($this->repository),
            id: $category->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());        
    }

    public function testStore() 
    {
        $useCase = new CategoryCreateUseCase($this->repository);        
        $request = new StoreCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'Category name',
            'description' => 'Category description',
        ]));
        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdate() 
    {
        $category = ModelCategory::factory()->create();

        $request = new UpdateCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'Category name',
        ]));

        $response = $this->controller->update(
            request: $request,
            useCase: new CategoryUpdateUseCase($this->repository),
            id: $category->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDelete() 
    {
        $category = ModelCategory::factory()->create();

        $response = $this->controller->destroy(
            useCase: new CategoryDeleteUseCase($this->repository),
            id: $category->id
        );
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
