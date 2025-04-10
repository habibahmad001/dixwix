<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class GiftoOrder extends Model
{
    use SoftDeletes;

    protected $table = 'gifto_orders';

    protected $fillable = [
        'user_id',
        'userEmail',
        'userName',
        'points',
        'giftoAmount',
        'giftoMsg',
        'campaignUuid',
        'selectedCard',
        'cardPath',
        'orderStatus',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Automatically set created_by and updated_by attributes.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->created_by = $user->name ?? 'user';
                $model->updated_by = $user->name ?? 'user';
                $model->user_id = $user->id;
            } else {
                $model->created_by = 'user';
                $model->updated_by = 'user';
            }
        });

        static::updating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->updated_by = $user->name ?? 'user';
            } else {
                $model->updated_by = 'user';
            }
        });
    }

    /**
     * Relation: GiftoOrder belongs to User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: GiftoOrder belongs to GiftoCampaign (by campaignUuid).
     */
    public function campaign()
    {
        return $this->belongsTo(GiftoCampaign::class, 'campaignUuid', 'uuid');
    }
}

