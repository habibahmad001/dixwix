<?php

namespace App\Validator;

trait User{

    protected $rules = [
        "email" =>"sometimes|required|email|unique:users",
        "name" => "required",
        "password" => "sometimes|required"
    ];

}