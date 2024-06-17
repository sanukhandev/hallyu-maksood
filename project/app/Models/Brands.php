<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;

    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $timestamps = false;

    protected $fillable = [
        'brand_name',
        'brand_logo',
        'brand_description',
        'brand_country',
        'brand_website',
        'brand_is_active',
        'brand_created_at',
        'brand_updated_at',
        'brand_deleted_at'
    ];


}
