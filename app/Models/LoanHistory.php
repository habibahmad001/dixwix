<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanHistory extends Model
{
    protected $fillable = ['user_id', 'book_id', 'group_id', 'reserved_at', 'due_date', 'status', 'amount', 'returned_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
