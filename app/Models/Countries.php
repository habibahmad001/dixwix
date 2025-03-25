<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Message\Error;
use App\Models\Model;
use Exception;

class Countries extends Model
{
    protected $primaryKey = "id";
    protected $table = "countries";
}
