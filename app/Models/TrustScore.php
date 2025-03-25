<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustScore extends Model
{
    protected $fillable = ['user_id', 'entry_id', 'book_id', 'rating', 'feedback'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
