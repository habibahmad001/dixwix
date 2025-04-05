<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftoCampaign extends Model
{
    use SoftDeletes;
    protected $table = 'gifto_campaigns';

    // Mass assignable fields
    protected $fillable = [
        'group_id',
        'compaign_uuid',
        'compaign_name',
        'compaign_denominations',
        'compaign_status',
        'card_bg',
        'card_title',
        'card_message',
        'status',
        'created_by',
        'updated_by',
    ];

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

//    protected $casts = [
//        'group_id' => 'array', // auto cast to array
//    ];

    /**
     * Scope a query to only include enabled campaigns.
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * Scope a query to only include non-deleted campaigns.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include deleted campaigns.
     */
    public function scopeTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * Scope a query to include all campaigns, even trashed ones.
     */
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    /**
     * Scope a query to only include enabled campaigns.
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }

    // If you need to manually control timestamps
    public $timestamps = true;

    /**
     * Boot method to hook into model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->check() ? auth()->user()->name : 'admin';
            $model->updated_by = auth()->check() ? auth()->user()->name : 'admin';
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->check() ? auth()->user()->name : 'admin';
        });
    }

    /**
     * Relationship with Group model.
     * Assuming there is a Group model and 'group_id' is the foreign key.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

//    /**
//     * Custom accessor to get related groups.
//     */
//    public function groups()
//    {
//        return Group::whereIn('id', $this->group_id)->get();
//    }
}

