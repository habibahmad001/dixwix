<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'type',
        'last4',
        'expiry_month',
        'expiry_year',
        'default'
    ];
}
