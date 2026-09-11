<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    /**
     * Seed the permission matrix with the built-in defaults.
     *
     * Each cell is `role = module granted`. The same defaults are mirrored in
     * App\Helpers\Qs::getModuleRoleMap() and act as the fallback for any role
     * that has no rows configured yet.
     *
     * @return void
     */
    public function run()
    {
        $map = [
            'Students' => ['super_admin', 'admin', 'teacher'],
            'Academics' => ['super_admin', 'admin', 'teacher', 'student'],
            'Analytics' => ['super_admin', 'admin', 'accountant', 'teacher'],
            'Users & Roles' => ['super_admin', 'admin'],
            'Classes & Subjects' => ['super_admin', 'admin'],
            'Exams & Marks' => ['super_admin', 'admin', 'teacher'],
            'Timetables' => ['super_admin', 'admin', 'teacher'],
            'Finance' => ['super_admin', 'admin', 'accountant'],
            'Pins' => ['super_admin'],
            'System Settings' => ['super_admin'],
        ];

        $roles = ['super_admin', 'admin', 'teacher', 'accountant', 'parent', 'student'];

        foreach ($roles as $role) {
            foreach ($map as $module => $allowedRoles) {
                DB::table('permissions')->updateOrInsert(
                    ['role' => $role, 'module' => $module],
                    ['allowed' => in_array($role, $allowedRoles, true) ? 1 : 0, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}