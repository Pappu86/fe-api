<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaPathGenerator extends DefaultPathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    protected function getBasePath(Media $media): string
    {
        $model = class_basename($media->model);
        return Str::snake(Str::pluralStudly($model)) . '/' . $media->getKey();
    }
}
