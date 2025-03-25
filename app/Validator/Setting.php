<?php

namespace App\Validator;

trait Setting{
    protected $rules = [
        "name" => "required",
        "value" => "required",
    ];
}
