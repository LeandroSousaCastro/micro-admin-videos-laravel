<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use Core\Category\Application\UseCase\ListUseCase as CategoryListUseCase;
use Core\Category\Application\Dto\ListOutputDto as CategoryListOutputDto;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class CategoryControllerUnitTest extends TestCase
{
    public function testIndex()
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('test');

        $mockListOutputDto = Mockery::mock(CategoryListOutputDto::class, [
            [], 1, 1, 1, 1, 1, 1, 1
        ]);


        $mockListUseCase = Mockery::mock(CategoryListUseCase::class);
        $mockListUseCase->shouldReceive('execute')->andReturn($mockListOutputDto);

        $controller = new CategoryController();
        $response = $controller->index(
            $mockRequest,
            $mockListUseCase
        );

        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);
        $mockListUseCase->shouldHaveReceived('execute');

        Mockery::close();
    }
}
