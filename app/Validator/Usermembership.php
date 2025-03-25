<?php

namespace App\Validator;

trait Usermembership{
    protected $rules = [
        "user_id" => "required",
        "plan_id" => "required",
    ];
}