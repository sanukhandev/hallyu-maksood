<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public function order()
    {
        return $this->hasOne('App\Models\Order','order_number','order_number');
    }
}
