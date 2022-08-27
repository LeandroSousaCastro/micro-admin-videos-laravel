<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaVideo extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'medias_videos';

    public $incrementing = false;

    protected $fillable = [
        'file_path',
        'encoded_path',
        'media_status',
        'type',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
