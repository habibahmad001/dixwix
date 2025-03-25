<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'user_id', 'review', 'rating'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'item_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
