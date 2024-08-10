<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Advertisement extends Model implements HasMedia
{
    use HasFactory, Searchable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'provider_id', 'position_id', 'title', 'type',
        'start_date', 'end_date', 'is_modal', 'is_external',
        'link', 'content', 'mobile_content', 'mobile_link',
        'clicks_count', 'is_auto_modal', 'auto_modal_duration',
        'position', 'has_mobile_content', 'is_multi_ads'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_modal' => 'boolean',
        'is_external' => 'boolean',
        'is_auto_modal' => 'boolean',
        'has_mobile_content' => 'boolean',
    ];
    
    /**
     * @return HasMany
     */
    public function getMultipleAds(): HasMany
    {
        return $this->hasMany(AdvertisementImage::class, 'advertisement_id', 'id');
    }

    /**
     * Register media collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('mobile_image')->singleFile();
        $this->addMediaCollection('video')->singleFile();
        $this->addMediaCollection('mobile_video')->singleFile();
        $this->addMediaCollection('document')->singleFile();
        $this->addMediaCollection('mobile_document')->singleFile();
    }
}