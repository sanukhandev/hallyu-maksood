<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorOrder extends Model
{
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }
    public function order()
    {
        return $this->belongsTo('App\Models\Order')->withDefault();
    }
}
