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
        $superadminRole=Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole=Role::firstOrCreate(['name' => 'Admin']);
        $userRole=Role::firstOrCreate(['name' => 'User']);

        // Create permissions
        // Create permissions
        $permissions = [
            'Keyword list',
            'Add keyword',
            'Edit keyword',
            'Delete keyword',
            'User list',
            'Create user',
            'Edit user',
            'Delete user',
            'Role list',
            'Create role',
            'Edit role',
            'Delete role',
            'Google API',
            // Backlink permissions
            'Backlink list',
            'Add backlink',
            'Edit backlink',
            'Delete backlink',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $permission = Permission::all();

        $userRole->givePermissionTo(['Keyword list','Add keyword','Edit keyword','Delete keyword']);
        $adminRole->givePermissionTo(['Add keyword','Edit keyword','Delete keyword','Keyword list','Create user','User list','Edit user','Delete user','Backlink list','Add backlink','Edit backlink','Delete backlink',]);
        $superadminRole->givePermissionTo($permission);

        // $superadmin = User::create([
        //     'name' => 'Super Admin',
        //     'email' => 'superadmin@example.com',
        //     'password' => static::$password ?? Hash::make('Shine@123'),
        // ]);
        // $superadmin->assignrole('super admin');

        // Create users and assign roles
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'Admin',
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'role' => 'User',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'name' => $userData['name'],
                'password' => static::$password ?? Hash::make('Shine@123'),
            ]);

            $user->assignRole($userData['role']);
        }
    }

}
