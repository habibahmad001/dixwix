<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUserInvited extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'group_id'];
}
