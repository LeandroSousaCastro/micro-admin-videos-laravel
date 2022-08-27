<?php

namespace App\Models;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at'
    ];

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime'
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function genres()
    {
        return $this->belongsTo(Genre::class);
    }

    public function castMembers()
    {
        return $this->belongsTo(CastMember::class, 'cast_member_video');
    }

    public function media()
    {
        return $this->hasOne(MediaVideo::class)
            ->where('type', MediaTypes::VIDEO->value);
    }

    public function trailer()
    {
        return $this->hasOne(MediaVideo::class)
            ->where('type', MediaTypes::TRAILER->value);
    }

    public function thumb()
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::THUMB->value);
    }

    public function thumbHalf()
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::THUMB_HALF->value);
    }

    public function banner()
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::BANNER->value);
    }
}
