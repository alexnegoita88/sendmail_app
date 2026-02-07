<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create first user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.ro',
            'password' => Hash::make('admin123'),
        ]);

        // Create second user
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.ro',
            'password' => Hash::make('manager123'),
        ]);
    }
}
