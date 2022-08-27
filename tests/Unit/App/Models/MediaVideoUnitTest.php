<?php

namespace Tests\Unit\App\Models;

use App\Models\MediaVideo;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaVideoUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new MediaVideo();
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
            'file_path',
            'encoded_path',
            'media_status',
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
