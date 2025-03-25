<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntuitToken extends Model
{
    protected $fillable = [
        'realm_id',
        'access_token',
        'refresh_token',
        'expires_at'
    ];
}
