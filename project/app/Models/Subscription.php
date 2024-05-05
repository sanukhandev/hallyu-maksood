<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['title','price','days','allowed_products','details'];
    public $timestamps = false;
}