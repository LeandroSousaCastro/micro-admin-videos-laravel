<?php

namespace App\Repositories\Eloquent\Traits;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Video\Domain\Entity\Video as EntityVideo;
use Illuminate\Database\Eloquent\Model;

trait VideoTrait
{
    protected Model $entityModel;

    public function updateMediaVideo(EntityVideo $entity): void
    {
        if ($mediaVideo = $entity->videoFile()) {
            $action = $this->model->media()->first() ? 'update' : 'create';
            $this->model->media()->{$action}([
                'file_path' => $mediaVideo->filePath,
                'media_status' => (string) $mediaVideo->mediaStatus->value,
                'encoded_path' => $mediaVideo->encodedPath,
                'type' => (string) MediaTypes::VIDEO->value,
            ]);
        }
    }

    public function updateMediaTrailer(EntityVideo $entity): void
    {
        if ($mediaTrailer = $entity->trailerFile()) {
            $action = $this->model->trailer()->first() ? 'update' : 'create';
            $this->model->trailer()->{$action}([
                'file_path' => $mediaTrailer->filePath,
                'media_status' => (string) $mediaTrailer->mediaStatus->value,
                'encoded_path' => $mediaTrailer->encodedPath,
                'type' => (string) MediaTypes::TRAILER->value,
            ]);
        }
    }

    public function updateImageBanner(EntityVideo $entity): void
    {
        if ($banner = $entity->bannerFile()) {
            $action = $this->model->banner()->first() ? 'update' : 'create';
            $this->model->banner()->{$action}([
                'path' => $banner->path(),
                'type' => (string) ImageTypes::BANNER->value,
            ]);
        }
    }

    public function updateImageThumb(EntityVideo $entity): void
    {
        if ($thumb = $entity->thumbFile()) {
            $action = $this->model->thumb()->first() ? 'update' : 'create';
            $this->model->thumb()->{$action}([
                'path' => $thumb->path(),
                'type' => (string) ImageTypes::THUMB->value,
            ]);
        }
    }

    public function updateImageThumbHalf(EntityVideo $entity): void
    {
        if ($thumbHalf = $entity->thumbHalf()) {
            $action = $this->model->thumbHalf()->first() ? 'update' : 'create';
            $this->model->thumbHalf()->{$action}([
                'path' => $thumbHalf->path(),
                'type' => (string) ImageTypes::THUMB_HALF->value,
            ]);
        }
    }
}
