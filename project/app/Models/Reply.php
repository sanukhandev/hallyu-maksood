<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = ['comment_id', 'user_id','text'];
    
    public function user()
    {
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function comment()
    {
    	return $this->belongsTo('App\Models\Comment')->withDefault();
    }

	public function subreplies()
	{
	     return $this->hasMany('App\Models\SubReply');
	}
}
