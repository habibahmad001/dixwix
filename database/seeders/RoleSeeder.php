<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $adminRole->givePermissionTo('all');
        $adminRole->givePermissionTo('all-items');
        $adminRole->givePermissionTo('all-groups');

        $userPermissions = [
            "dashboard",
            "my-items",
            "add-book",
            "edit-book",
            "store-book",
            "search-item",
            "import-item-csv",
            "add-group",
            "store-group",
            "my-groups",
            "join-group",
            "my-rewards",
        ];

        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }
    }
}
