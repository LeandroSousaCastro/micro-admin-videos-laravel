<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\{
    StoreCategoryRequest,
    UpdateCategoryRequest
};
use Core\Category\Application\Dto\{
    ListInputDto,
    CreateInputDto,
    DeleteInputDto,
    GetInputDto,
    UpdateInputDto
};
use Core\Category\Application\UseCase\{
    GetUseCase,
    ListUseCase,
    CreateUseCase,
    UpdateUseCase,
    DeleteUseCase
};
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request, ListUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPage: (int) $request->get('total_page', 15),
            )
        );

        return CategoryResource::collection($response->items)
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'current_page' => $response->current_page,
                    'last_page' => $response->last_page,
                    'first_page' => $response->first_page,
                    'per_page' => $response->per_page,
                    'to' => $response->to,
                    'from' => $response->from,
                ]
            ]);
    }

    public function show(GetUseCase $useCase, $id)
    {
        $category = $useCase->execute(
            new GetInputDto($id)
        );

        return (new CategoryResource($category))
            ->response();
    }

    public function store(StoreCategoryRequest $request, CreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CreateInputDto(
                name: $request->name,
                description: $request->description ?? '',
                isActive: (bool) $request->is_active ?? true,
            )
        );

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCategoryRequest $request, UpdateUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            input: new UpdateInputDto(
                id: $id,
                name: $request->name
            )
        );

        return (new CategoryResource($response))
            ->response();
    }

    public function destroy(DeleteUseCase $useCase, $id)
    {
        $useCase->execute(new DeleteInputDto($id));
        return response()->noContent();
    }
}
