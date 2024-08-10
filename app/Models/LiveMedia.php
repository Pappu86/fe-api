<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LiveMedia extends Model implements HasMedia
{
    use HasFactory, Translatable, InteractsWithMedia, LogsActivity;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title', 'subtitle'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'image', 'content',
        'featured', 'start_at', 'end_at'
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
        'featured' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Register media collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
