<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'points',
        'status',
        'approved_by',
        'approved_at'
    ];

    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    public static $STATUS_TEXT = [
        self::PENDING   => "Pending",
        self::APPROVED  => "Approved",
        self::REJECTED  => "Rejected",
    ];

    public function getStatusTextAttribute()
    {
        return self::$STATUS_TEXT[$this->status] ?? "-";
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function approveUser(){

        return $this->belongsTo(User::class, 'approved_by');
    }
}
