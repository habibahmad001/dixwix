<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRejectedRequest extends Model
{
    protected $fillable = [
        'entry_id',
        'user_id',
        'book_id',
        'reason',
        'disapproved_by',
        'disapproved_at',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disapprover()
    {
        return $this->belongsTo(User::class, 'disapproved_by');
    }
}
