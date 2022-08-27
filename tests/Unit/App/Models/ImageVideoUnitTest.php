<?php

namespace Tests\Unit\App\Models;

use App\Models\ImageVideo;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImageVideoUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new ImageVideo();
    }

    protected function traits(): array
    {
        return [
            HasFactory::class,
            UuidTrait::class,
        ];
    }

    protected function fillable(): array
    {
        return [
            'path',
            'type',
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
