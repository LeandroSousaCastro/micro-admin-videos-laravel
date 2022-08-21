<?php

namespace Tests\Unit\App\Models;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CastMemberUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new CastMember();
    }

    protected function traits(): array
    {
        return [
            HasFactory::class,
            SoftDeletes::class
        ];
    }

    protected function fillable(): array
    {
        return [
            'id',
            'name',
            'type',
            'created_at'
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'deleted_at' => 'datetime'
        ];
    }
}
