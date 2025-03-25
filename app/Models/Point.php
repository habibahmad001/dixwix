<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'type',
        'points',
        'amount',
        'description',
    ];

    const TRANS_TYPE_REWARD = 1;
    const TRANS_TYPE_PURCHASED = 2;
    const TRANS_TYPE_AUTO = 3;
    const TRANS_TYPE_GIFT = 4;

    public static $TRANS_TYPE_TEXT = [
        self::TRANS_TYPE_REWARD   => "Reward",
        self::TRANS_TYPE_PURCHASED  => "Purchase",
        self::TRANS_TYPE_AUTO  => "Auto Transfer",
        self::TRANS_TYPE_GIFT  => "Gifted",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(CoinPackage::class, 'package_id');
    }

}
