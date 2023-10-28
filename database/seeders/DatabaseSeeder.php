<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         User::factory()->create([
             'first_name' => 'Bartosz',
             'last_name' => 'Piechowiak',
             'email' => 'test@timelyrecord.com',
             'password' => bcrypt('12345678')
         ]);
    }
}
