<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'tahsinmoin2662@gmail.com');
        $adminPassword = env('ADMIN_PASSWORD', 'Tahsinmoin1447');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'NurseSheba Admin',
                'email' => $adminEmail,
                'phone' => '01700000000',
                'address' => 'Dhaka',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );
    }
}
