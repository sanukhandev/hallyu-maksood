<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{

	public function sent()
	{
	    return $this->belongsTo('App\Models\User', 'sent_user');
	}

	public function recieved()
	{
	    return $this->belongsTo('App\Models\User', 'recieved_user');
	}

	public function messages()
	{
	    return $this->hasMany('App\Models\Message');
	}

}
