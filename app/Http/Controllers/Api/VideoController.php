<?php

namespace App\Http\Controllers\Api;

use App\Adapters\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\{
    StoreVideoRequest,
    UpdateVideoRequest
};
use Core\Video\Application\{
    Dto\GetInputDto,
    Dto\ListInputDto,
    Dto\CreateInputDto,
    Dto\DeleteInputDto,
    Dto\UpdateInputDto,
    UseCase\ListUseCase,
    UseCase\GetUseCase,
    UseCase\CreateUseCase,
    UseCase\UpdateUseCase,
    UseCase\DeleteUseCase,
};
use Core\Video\Domain\Enum\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    public function index(Request $request, ListUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListInputDto(
                filter: $request->filter ?? '',
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPage: (int) $request->get('per_page', 15)
            )
        );

        return (new ApiAdapter($response))->toJson();
    }

    public function show(GetUseCase $useCase, $id)
    {
        $response = $useCase->execute(new GetInputDto($id));

        return ApiAdapter::json($response);
    }

    public function store(CreateUseCase $useCase, StoreVideoRequest $request)
    {
        $response = $useCase->execute(new CreateInputDto(
            title: $request->title,
            description: $request->description,
            yearLaunched: $request->year_launched,
            duration: $request->duration,
            opened: $request->opened,
            rating: Rating::from($request->rating),
            categories: $request->categories,
            genres: $request->genres,
            castMembers: $request->cast_members,
            thumbFile: getArrayFile($request->file('thumb_file')),
            thumbHalf: getArrayFile($request->file('thumb_half_file')),
            bannerFile: getArrayFile($request->file('banner_file')),
            trailerFile: getArrayFile($request->file('trailer_file')),
            videoFile: getArrayFile($request->file('video_file')),
        ));

        return ApiAdapter::json($response, Response::HTTP_CREATED);
    }

    public function update(UpdateUseCase $useCase, UpdateVideoRequest $request, $id)
    {
        $response = $useCase->execute(new UpdateInputDto(
            id: $id,
            title: $request->title,
            description: $request->description,
            categories: $request->categories,
            genres: $request->genres,
            castMembers: $request->cast_members,
            videoFile: getArrayFile($request->file('video_file')),
            trailerFile: getArrayFile($request->file('trailer_file')),
            bannerFile: getArrayFile($request->file('banner_file')),
            thumbFile: getArrayFile($request->file('thumb_file')),
            thumbHalf: getArrayFile($request->file('thumb_half_file')),
        ));

        return ApiAdapter::json($response);
    }

    public function destroy(DeleteUseCase $useCase, $id)
    {
        $useCase->execute(new DeleteInputDto(id: $id));
        return response()->noContent();
    }
}
