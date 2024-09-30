<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Device;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::factory()->create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@material.com',
            'password' => ('12345678'),
            'level' => 'Admin',
            'avatar' => 'default-avatar.jpg'
        ]);

        Device::factory()->create([
            'name' => 'Đèn',
            'status' => 0,
            'last_toggled_at' => now()
        ]);
        
        Device::factory()->create([
            'name' => 'Quạt',
            'status' => 0,
            'last_toggled_at' => now()
        ]);

        Device::factory()->create([
            'name' => 'Điều hòa',
            'status' => 0,
            'last_toggled_at' => now()
        ]);
    }
}
