<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reporter extends Model implements HasMedia
{
    use HasFactory, Translatable, InteractsWithMedia;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'username', 'email',
        'mobile', 'avatar', 'social_links',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'social_links' => 'json',
    ];

    /**
     * Register media collection
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    /**
     * @return HasMany
     */
    public function post(): HasMany
    {
        return $this->hasMany(Post::class)
            ->with([
                'translations',
                'category',
                'category.translations',
            ])
            ->where('status', '=', 1)
            ->whereDate('datetime', '<=', now())
            ->orderByDesc('datetime');
    }
}
