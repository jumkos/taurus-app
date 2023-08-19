<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = [
        'issuer_id',
        'refer_id',
        'cust_name',
        'phone',
        'address',
        'offering_date',
        'product_type_id',
        'product_category_id',
        'product_id',
        'nominal',
        'info',
    ];
}
