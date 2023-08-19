<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'referral_id',
        'date',
        'status_id',
        'detail',
    ];
}
