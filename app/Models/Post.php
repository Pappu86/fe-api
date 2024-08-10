<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, Translatable, InteractsWithMedia, LogsActivity;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title', 'slug', 'short_title', 'shoulder',
        'hanger', 'excerpt', 'content', 'caption', 'source',
        'meta_title', 'meta_description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'user_id', 'reporter_id', 'category_id',
        'type', 'image', 'meta_image', 'views_count',
        'is_fb_article', 'is_edited', 'datetime',
    ];

    /**
     * @var array
     */
    public $meiliSearchSettings = [
        "updateDisplayedAttributes" => [
            'id',
            'title',
            'shortTitle',
            'slug',
            'excerpt',
            'publishedAt',
            'image',
            'reporter',
            'category',
            'categorySlug',            
        ],
        "updateSearchableAttributes" => [
            'title',
            'shortTitle',
            'excerpt',
            'category',
            'reporter',
            // 'content'
        ],
        "updateFilterableAttributes" => [
            'publishedAt',
            'categoryId',
            'reporterId'
        ],
        "updateSortableAttributes" => [
            'publishedAt'
        ],
        "updateRankingRules" => [
            'words',
            'sort',
            'typo',
            'proximity',
            'attribute',
            'exactness',
            'publishedAt:asc',
            'publishedAt:desc'
        ]
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
        'is_fb_article' => 'boolean',
        'is_edited'=>'boolean',
        'datetime' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Reporter::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Register media collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('meta_image')->singleFile();
        $this->addMediaCollection('assets');
    }
}