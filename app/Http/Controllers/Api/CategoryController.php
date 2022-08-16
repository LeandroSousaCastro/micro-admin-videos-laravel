<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Core\Category\Application\UseCase\ListUseCase as CategoryListUseCase;
use Core\Category\Application\Dto\ListInputDto as CategoryListInputDto;
use Core\Category\Application\UseCase\CreateUseCase as CategoryCreateUseCase;
use Core\Category\Application\Dto\CreateInputDto as CategoryCreateInputDto;
use Core\Category\Application\Dto\DeleteInputDto as CategoryDeleteInputDto;
use Core\Category\Application\UseCase\GetUseCase as CategoryGetUseCase;
use Core\Category\Application\Dto\GetInputDto as CategoryGetInputDto;
use Core\Category\Application\UseCase\UpdateUseCase as CategoryUpdateUseCase;
use Core\Category\Application\Dto\UpdateInputDto as CategoryUpdateInputDto;
use Core\Category\Application\UseCase\DeleteUseCase as CategoryDeleteUseCase;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request, CategoryListUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CategoryListInputDto(
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
                    'last_page' => $response->last_page,
                    'first_page' => $response->first_page,
                    'per_page' => $response->per_page,
                    'to' => $response->to,
                    'from' => $response->from,
                ]
            ]);
    }

    public function show(CategoryGetUseCase $useCase, $id)
    {
        $category = $useCase->execute(
            new CategoryGetInputDto($id)
        );

        return (new CategoryResource(collect($category)))
                    ->response();
    }

    public function store(StoreCategoryRequest $request, CategoryCreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CategoryCreateInputDto(
                name: $request->name,
                description: $request->description ?? '',
                isActive: (bool) $request->is_active ?? true,
            )
        );

        return (new CategoryResource(collect($response)))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCategoryRequest $request, CategoryUpdateUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            input: new CategoryUpdateInputDto(
                id: $id,
                name: $request->name
            )
        );

        return (new CategoryResource(collect($response)))
                    ->response();
    }

    public function destroy(CategoryDeleteUseCase $useCase, $id)
    {
        $useCase->execute(new CategoryDeleteInputDto($id));
        return response()->noContent();
    }
}
