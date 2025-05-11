<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeReviews extends Model
{
    // Explicitly define the table if it doesn't follow Laravel's pluralization convention
    protected $table = 'home_reviews';

    // Optionally define fillable fields for mass assignment
    protected $fillable = [
        'name',
        'role',
        'avatar',
        'textDescription',
    ];
}
