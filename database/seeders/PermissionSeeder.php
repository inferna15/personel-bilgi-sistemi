<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissions = [
            'Admin' => [
                'view users',
                'create users',
                'update users',
                'delete users',
                'view units',
                'create units',
                'update units',
                'delete units',
                'view leaves',
                'create leaves',
                'update leaves',
                'delete leaves',
                'review leaves',
                'view cars',
                'create cars',
                'update cars',
                'delete cars',
                'view cards',
                'create cards',
                'update cards',
                'delete cards',
                'view announcements',
                'create announcements',
                'update announcements',
                'delete announcements',
                'view salaries',
                'create salaries',
                'update salaries',
                'delete salaries',
            ],
            'Yönetici' => [
                'view users',
                'create users',
                'update users',
                'delete users',
                'view units',
                'update units',
                'view leaves',
                'create leaves',
                'update leaves',
                'review leaves',
                'view cars',
                'create cars',
                'update cars',
                'delete cars',
                'view cards',
                'create cards',
                'update cards',
                'delete cards',
                'view announcements',
                'create announcements',
                'update announcements',
                'delete announcements',
                'view salaries',
                'create salaries',
                'update salaries',
                'delete salaries',
            ],
            'Personel' => [],
        ];

        $permissions = collect($rolePermissions)->flatten()->unique();

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        foreach ($rolePermissions as $roleName => $permissionList) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($permissionList);
        }
    }
}
