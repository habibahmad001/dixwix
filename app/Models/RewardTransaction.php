<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'coins',
        'amount',
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

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approveUser(){
        return $this->belongsTo(User::class, 'approved_by');
    }
}
