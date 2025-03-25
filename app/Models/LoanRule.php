<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRule extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration',
    ];
}
