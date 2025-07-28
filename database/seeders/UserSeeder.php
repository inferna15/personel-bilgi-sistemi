<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcı
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'One',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'identity_number' => '12345678901',
            'birth_date' => '2000-01-01',
            'gender' => 'Erkek',
            'phone' => '5551112233',
            'address' => 'Admin Address',
        ])->assignRole('Admin');

        // Manager kullanıcı
        User::create([
            'first_name' => 'Manager',
            'last_name' => 'One',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'identity_number' => '12345678902',
            'birth_date' => '1995-03-15',
            'gender' => 'Erkek',
            'phone' => '5551112234',
            'address' => 'Manager Address',
        ])->assignRole('Yönetici');

        // Staff kullanıcı
        User::create([
            'first_name' => 'Staff',
            'last_name' => 'One',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'identity_number' => '12345678903',
            'birth_date' => '1998-06-10',
            'gender' => 'Kadın',
            'phone' => '5551112235',
            'address' => 'Staff Address',
        ])->assignRole('Personel');
    }
}
