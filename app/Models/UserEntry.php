<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEntry extends Model
{
    // If your table is not named 'user_entries' (Laravel's default plural), define it:
    protected $table = 'user_entry';

    // Allow mass assignment for relevant fields
    protected $fillable = [
        'user_id',
        'entry_id',
        'started_date',
        'end_date',
    ];

    /**
     * Get the user that owns the entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book entry associated with the user entry.
     */
    public function bookEntry(): BelongsTo
    {
        return $this->belongsTo(Entries::class, 'entry_id');
    }
}
