<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayPalOrder extends Model
{
    use HasFactory;

    protected $primaryKey = "paypal_order_id";

    protected $guarded = [
        'paypal_order_id'
    ];

}
