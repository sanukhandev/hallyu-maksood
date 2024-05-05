<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = false;
    protected $fillable = ['state','country_id','status','tax'];
    public function country()
    {
    	return $this->belongsTo('App\Models\Country')->withDefault();
    }

}
