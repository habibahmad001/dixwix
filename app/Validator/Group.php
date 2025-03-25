<?php

namespace App\Validator;

trait Group{
    protected $rules = [
        "title" => "required",
        "group_picture" => "required"
    ];
}
