<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class User_Role_Permition extends Seeder
{


    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user  = Role::firstOrCreate(['name' => 'user']);

        // Permissions
        $permissions = [
            'manage flights',
            'manage booking',
            'create booking',
            'update booking',
            'cancel booking',
            'create payment',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions
        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo([
            'create booking',
            'update booking',
            'cancel booking',
            'create payment',
        ]);
    }
}
