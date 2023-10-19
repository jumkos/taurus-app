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
        'product_detail',
        'relation',
        'referantor',
        'contact_person',
        'issuer_rating',
        'issuer_comment',
        'refer_rating',
        'refer_comment',
        'refer_to_division',
        'refer_to_region',
        'refer_to_city'
    ];

    public function getRefID()
    {
        $id = $this->id;
        $alphabet = range('A', 'Z');
        return sprintf('%s-%05d%s', $alphabet[substr($id, -1)], $this->id, $alphabet[substr($id, 0, 1)]);
    }
}
