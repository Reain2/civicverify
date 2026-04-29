<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Konsultan
        User::create([
            'name'     => 'Konsultan Demo',
            'email'    => 'konsultan@civicverify.id',
            'password' => Hash::make('password'),
            'role'     => 'konsultan',
        ]);

        // Surveyor
        User::create([
            'name'     => 'Surveyor Demo',
            'email'    => 'surveyor@civicverify.id',
            'password' => Hash::make('password'),
            'role'     => 'surveyor',
        ]);

        // Kementerian
        User::create([
            'name'     => 'Kementerian Demo',
            'email'    => 'kementerian@civicverify.id',
            'password' => Hash::make('password'),
            'role'     => 'kementerian',
        ]);

        // Masyarakat (default)
        User::create([
            'name'     => 'Warga Demo',
            'email'    => 'warga@civicverify.id',
            'password' => Hash::make('password'),
            'role'     => 'masyarakat',
        ]);
    }
}
