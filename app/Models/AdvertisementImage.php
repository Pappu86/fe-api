<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementImage extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advertisement_id', 'src',
    ];

    /**
     * Set timestamps false.
     *
     * @var bool
     */
    public $timestamps = false;
}