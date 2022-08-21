<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMember;
use App\Http\Requests\UpdateCastMember;
use App\Http\Resources\CastMemberResource;
use Core\CastMember\Application\Dto\{
    CreateInputDto,
    DeleteInputDto,
    GetInputDto,
    ListInputDto,
    UpdateInputDto
};
use Core\CastMember\Application\UseCase\{
    CreateUseCase,
    DeleteUseCase,
    GetUseCase,
    ListUseCase,
    UpdateUseCase
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
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

        return CastMemberResource::collection($response->items)
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

        return (new CastMemberResource($category))
            ->response();
    }

    public function store(StoreCastMember $request, CreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CreateInputDto(
                name: $request->name,
                type: (int) $request->type
            )
        );

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCastMember $request, UpdateUseCase $useCase, $id)
    {
        $response = $useCase->execute(
            input: new UpdateInputDto(
                id: $id,
                name: $request->name,
                type: (int) $request->type
            )
        );

        return (new CastMemberResource($response))
            ->response();
    }

    public function destroy(DeleteUseCase $useCase, $id)
    {
        $useCase->execute(new DeleteInputDto($id));
        return response()->noContent();
    }
}
