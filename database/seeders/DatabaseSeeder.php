<?php

namespace Database\Seeders;

use App\Models\keyword;
use App\Models\User;
use App\Models\AdminSetting;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\keywordFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private static string $password;

    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        // Create the first user with role 'admin'
        $superadminRole=Role::create(['name' => 'Super Admin']);
        $adminRole=Role::create(['name' => 'Admin']);
        $userRole=Role::create(['name' => 'User']);

        // Create permissions
        Permission::create(['name' => 'Add keyword']);
        Permission::create(['name' => 'Edit keyword']);
        Permission::create(['name' => 'Delete keyword']);
        Permission::create(['name' => 'Keyword list']);
        Permission::create(['name' => 'Create user']);
        Permission::create(['name' => 'Edit user']);
        Permission::create(['name' => 'Delete user']);
        Permission::create(['name' => 'User list']);
        Permission::create(['name' => 'Google API']);
        Permission::create(['name' => 'Role list']);
        Permission::create(['name' => 'Create role']);
        Permission::create(['name' => 'Edit role']);
        Permission::create(['name' => 'Delete role']);

        $permission = Permission::all();

        $userRole->givePermissionTo(['Keyword list','Add keyword','Edit keyword','Delete keyword']);
        $adminRole->givePermissionTo(['Add keyword','Edit keyword','Delete keyword','Keyword list','Create user','User list','Edit user','Delete user']);
        $superadminRole->givePermissionTo($permission);

        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => static::$password ?? Hash::make('Shine@123'),
        ]);
        $superadmin->assignrole('super admin');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => static::$password ?? Hash::make('Shine@123'),
        ]);

        $admin->assignRole('admin');

        $user=User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => static::$password ?? Hash::make('Shine@123'),
        ]);
        $user->assignRole('user');
    }

}
