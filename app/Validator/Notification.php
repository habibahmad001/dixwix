<?php

namespace App\Validator;

trait Notification{
    protected $rules = [
        "ref_type" => "required",
        "ref_id" => "required",
    ];
}