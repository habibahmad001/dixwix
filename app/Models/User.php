<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class);
    // }
    public function usergroups()
    {
        return $this->hasMany('App\Models\Groupmember', 'member_id', 'id');
    }

    public function usergrouptype()
    {
        return $this->hasOne('App\Models\Grouptype', 'id', 'group_type');
    }

    public function createdgroups()
    {
        return $this->hasMany('App\Models\Group', 'created_by', 'id');
    }

    public function createditems()
    {
        return $this->hasMany('App\Models\Book', 'created_by', 'id');
    }

    public function membership()
    {
        return $this->hasMany('App\Models\Usermembership', 'user_id', 'id')->where("is_active", 1);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function subscription()
    {
        return $this->hasMany(Usermembership::class);
    }

    public function trustscores()
    {
        return $this->hasMany(TrustScore::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function rewardTransactions()
    {
        return $this->hasMany(RewardTransaction::class);
    }

    public function transferPointRequestToUser()
    {
        return $this->hasMany(TransferRequest::class, 'to_user_id');
    }

    public function transferPointRequestFromUser()
    {
        return $this->hasMany(TransferRequest::class, 'from_user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (is_null($user->group_locations)) {
                $user->group_locations = json_encode(['Office', 'Community Center', 'Library', 'Park']);
            }
        });
    }

}
