<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('users')->insert([
                'name' => 'admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => hash::make('123'),
                'created_at' => now(),
                'updated_at' => now(),

        ]);
    }
}
