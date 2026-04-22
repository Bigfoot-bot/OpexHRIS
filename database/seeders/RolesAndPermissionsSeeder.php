<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Employees
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',

            // Leave
            'view leave',
            'create leave',
            'approve leave',
            'delete leave',

            // Payroll
            'view payroll',
            'create payroll',
            'approve payroll',
            'download payslips',

            // Performance
            'view performance',
            'create performance',
            'edit performance',

            // Licenses
            'view licenses',
            'create licenses',
            'edit licenses',
            'delete licenses',

            // Recruitment
            'view recruitment',
            'create recruitment',
            'edit recruitment',
            'delete recruitment',

            // Disciplinary
            'view disciplinary',
            'create disciplinary',
            'edit disciplinary',

            // Grievances
            'view grievances',
            'create grievances',
            'edit grievances',

            // Training
            'view training',
            'create training',
            'edit training',

            // Reports
            'view reports',

            // Settings
            'manage settings',

            // Users
            'manage users',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // HR Admin — full access
        $hrAdmin = Role::firstOrCreate(['name' => 'HR Admin', 'guard_name' => 'web']);
        $hrAdmin->syncPermissions(Permission::where('guard_name', 'web')->get());

        // HR Manager — most access except settings & users
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        $hrManager->syncPermissions([
            'view employees', 'create employees', 'edit employees',
            'view leave', 'create leave', 'approve leave',
            'view payroll', 'create payroll', 'approve payroll', 'download payslips',
            'view performance', 'create performance', 'edit performance',
            'view licenses', 'create licenses', 'edit licenses',
            'view recruitment', 'create recruitment', 'edit recruitment',
            'view disciplinary', 'create disciplinary', 'edit disciplinary',
            'view grievances', 'create grievances', 'edit grievances',
            'view training', 'create training', 'edit training',
            'view reports',
        ]);

        // Department Head — view + limited actions
        $deptHead = Role::firstOrCreate(['name' => 'Department Head', 'guard_name' => 'web']);
        $deptHead->syncPermissions([
            'view employees',
            'view leave', 'approve leave',
            'view payroll',
            'view performance', 'create performance', 'edit performance',
            'view licenses',
            'view recruitment',
            'view disciplinary', 'create disciplinary',
            'view grievances',
            'view training',
            'view reports',
        ]);

        // Employee — view only
        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employee->syncPermissions([
            'view leave', 'create leave',
            'view payroll', 'download payslips',
            'view performance',
            'view training',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}