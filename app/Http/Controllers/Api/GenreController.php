<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{
    StoreGenre,
    UpdateGenre
};
use App\Http\Resources\GenreResource;
use Core\Genre\Application\Dto\{
    CreateInputDto,
    DeleteInputDto,
    GetInputDto,
    ListInputDto,
    UpdateInputDto
};
use Core\Genre\Application\UseCase\{
    CreateUseCase,
    DeleteUseCase,
    GetUseCase,
    ListUseCase,
    UpdateUseCase
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ListUseCase $useCase)
    {
        $response = $useCase->execute(
            inputDto: new ListInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPage: (int) $request->get('total_page', 15),
            )
        );

        return GenreResource::collection($response->items)
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\StoreGenre  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGenre $request, CreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CreateInputDto(
                name: $request->name,
                isActive: (bool) $request->is_active ?? true,
                categoriesId: $request->categories_id ?? [],
            )
        );

        return (new GenreResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GetUseCase $useCase, $id)
    {
        $category = $useCase->execute(
            new GetInputDto($id)
        );

        return (new GenreResource($category))
            ->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGenre $request, UpdateUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            input: new UpdateInputDto(
                id: $id,
                name: $request->name,
                categoriesId: $request->categories_id ?? [],
            )
        );

        return (new GenreResource($response))
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUseCase $useCase, $id)
    {
        $useCase->execute(new DeleteInputDto($id));
        return response()->noContent();
    }
}
