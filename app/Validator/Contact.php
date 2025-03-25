<?php

namespace App\Validator;

trait Contact{
    protected $rules = [
        "name" => "required",
        "email" => "required|email",
    ];
}