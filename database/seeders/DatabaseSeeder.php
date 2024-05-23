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

        $userRole->givePermissionTo(['Keyword list','Add keyword']);
        $adminRole->givePermissionTo(['Add keyword','Edit keyword','Delete keyword','Keyword list']);
        $superadminRole->givePermissionTo(['Add keyword','Edit keyword','Delete keyword','Keyword list']);

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
