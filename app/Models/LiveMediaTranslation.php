<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveMediaTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'live_media_id','title', 'subtitle',
    ];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
