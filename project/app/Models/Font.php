<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    protected $fillable = ['is_default','font_family','font_value'];
    protected $table    = 'fonts';

    public $timestamps  = false;
}
