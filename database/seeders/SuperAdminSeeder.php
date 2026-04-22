<?php

namespace Database\Seeders;

use App\Models\Central\SuperAdmin;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::create([
            'name'     => 'OpEx Super Admin',
            'email'    => 'admin@opexhealth.com',
            'password' => bcrypt('Admin@1234'),
        ]);
    }
}