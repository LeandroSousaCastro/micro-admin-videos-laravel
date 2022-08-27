<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVideo extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'images_videos';

    protected $fillable = [
        'path',
        'type',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string'
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
