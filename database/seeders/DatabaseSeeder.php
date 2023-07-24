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
            'admin' => 1,
            'user' => 1,
            'street' => '12 Arrowhead St. Dayton, OH 45420',
            'suite' => 'Suite 776',
            'city' => 'Huelstown',
            'zip_code' => '87804',
            'latitude' => '40.712345',
            'longitude' => '-74.006789',
            'otp' => null,
        ]);


        // Use this for dummy user data
        // $this->call([
        //     UserSeeder::class,
        // ]);
    }
}
