<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'title', 'slug', 'short_title',
        'shoulder', 'hanger', 'excerpt', 'content',
        'caption', 'source', 'meta_title', 'meta_description',
    ];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
