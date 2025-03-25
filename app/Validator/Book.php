<?php

namespace App\Validator;

trait Book{

    protected $rules = [
        "name" => "required|string",
        "item_id" => "required|unique:App\Models\Book,item_id",
        "description" => "required",
        // "group_type_id" => "required",
        "year" => "required|numeric",
        //"pages" => "required|numeric",
        "copies" => "required|numeric",
        "type_id" => "required",
        "price" => "required",
        "rent_price" => "required",
        "locations" => "required",
        "cover_page" => "required"
    ];

}
