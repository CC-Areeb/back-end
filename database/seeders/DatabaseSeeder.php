<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "Areeb Malik",
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'username' => 'admin',
            'super_admin' => 1,
            'otp' => null,
        ]);
        $this->call(UserSeeder::class);
        $this->call(PostSeeder::class);
    }
}
