<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrivalSection extends Model
{
    use HasFactory;

    protected $fillable = ['title','header', 'photo', 'status','position','created_at', 'updated_at'];

}
