<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@grahaindah.go.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        User::create([
            'name' => 'Petugas Pelayanan 1',
            'username' => 'petugas1',
            'email' => 'petugas1@grahaindah.go.id',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
            'status' => 'aktif',
        ]);
    }
}
