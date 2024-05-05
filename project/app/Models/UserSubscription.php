<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
   protected $fillable = ['user_id', 'subscription_id', 'title', 'currency_sign', 'currency_code','currency_value', 'price', 'days', 'allowed_products', 'details', 'method', 'txnid', 'charge_id', 'flutter_id', 'created_at', 'updated_at', 'status','payment_number'];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }
}
