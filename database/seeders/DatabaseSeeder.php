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
        $adminRole=Role::create(['name' => 'admin']);
        $userRole=Role::create(['name' => 'user']);

        // Create permissions
        Permission::create(['name' => 'write keyword']);
        Permission::create(['name' => 'edit keyword']);

        $userRole->givePermissionTo('write keyword');
        $adminRole->givePermissionTo(['write keyword','edit keyword']);


        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => static::$password ?? Hash::make('password'),
        ]);

        // Assign 'admin' role to the first user
        $admin->assignRole('admin');

        $user=User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => static::$password ?? Hash::make('password'),
        ]);
        $user->assignRole('user');
    }

}
