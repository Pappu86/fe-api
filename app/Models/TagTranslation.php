<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id', 'name', 'slug',
        'meta_title', 'meta_description',
    ];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
