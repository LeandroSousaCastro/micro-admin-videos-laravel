<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'images_videos';

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
