<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seotool extends Model
{
    protected $fillable = ['google_analytics','facebook_pixel','meta_keys','meta_description'];
    public $timestamps = false;
}
