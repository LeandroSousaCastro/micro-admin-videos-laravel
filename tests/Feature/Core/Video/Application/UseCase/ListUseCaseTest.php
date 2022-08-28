<?php

namespace Tests\Feature\Core\Video\Application\UseCase;

use App\Models\Video;
use Core\Video\Application\Dto\{
    ListInputDto,
};
use Core\Video\Application\UseCase\ListUseCase;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Tests\TestCase;

class ListUseCaseTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testPagination(
        int $total,
        int $perPage,
    ) {
        Video::factory()->count($total)->create();

        $useCase = new ListUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $response = $useCase->execute(new ListInputDto(
            filter: '',
            order: 'desc',
            page: 1,
            totalPage: $perPage
        ));

        $this->assertCount($perPage, $response->items());
        $this->assertEquals($total, $response->total());
    }

    protected function provider(): array
    {
        return [
            [
                'total' => 30,
                'perPage' => 10,
            ], [
                'total' => 20,
                'perPage' => 5,
            ], [
                'total' => 0,
                'perPage' => 0,
            ],
        ];
    }
}
