<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'name', 'slug',
        'meta_title', 'meta_description',
    ];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
