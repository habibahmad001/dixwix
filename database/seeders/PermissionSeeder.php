<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            "all",
            "dashboard",
            "my-items",
            "add-book",
            "edit-book",
            "store-book",
            "search-item",
            "import-item-csv",
            "add-group",
            "store-group",
            "all-items",
            "my-groups",
            "view-lender-group",
            "view-borrower-group",
            "all-groups",
            "join-group",
            "my-rewards",
        ];

        foreach($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }
    }
}
