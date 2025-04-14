<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = ['manage users', 'manage roles', 'manage permissions'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        // Assign no permissions or limited ones to user role
        $userRole->syncPermissions([]);

        // Create Admin User
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        // Create Standard User
        $user = User::firstOrCreate([
            'email' => 'user@example.com',
        ], [
            'name' => 'Standard User',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole($userRole);
    }
}
