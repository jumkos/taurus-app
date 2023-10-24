<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'user_id', 'division_id', 'region_id', 'city_id', 'phone'
    ];

    protected $hidden = [
        'id', 'created_at','updated_at','user_id',
    ];
}
