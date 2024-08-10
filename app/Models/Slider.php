<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Slider extends Model implements HasMedia
{
    use HasFactory, Translatable, InteractsWithMedia, LogsActivity;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'type', 'content', 'link', 'is_external', 'ordering',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
        // Chain fluent methods for configuration options
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'is_external' => 'boolean',
    ];

    /**
     * Register media collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsFile(function (File $file) {
                return $file->mimeType === 'image/jpeg'
                    || $file->mimeType === 'image/png'
                    || $file->mimeType === 'image/gif'
                    || $file->mimeType === 'image/svg+xml'
                    || $file->mimeType === 'image/webp'
                    || $file->mimeType === 'image/bmp';
            });

        $this->addMediaCollection('video')
            ->singleFile()
            ->acceptsFile(function (File $file) {
                return $file->mimeType === 'video/mpeg'
                    || $file->mimeType === 'video/ogg'
                    || $file->mimeType === 'video/3gpp'
                    || $file->mimeType === 'video/3gpp2'
                    || $file->mimeType === 'video/webm'
                    || $file->mimeType === 'video/mp4';
            });
    }
}
