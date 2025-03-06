<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create(); creates 10 fake records

        \App\Models\User::insert([
            ['name' => 'testOne',
            'email' => 'test1@example.com',
            'password'=>bcrypt('123456')],
       
            ['name'=>'testTwo',
            'email'=>'test2@example.com',
            'password'=>bcrypt('666666')]
        ]);
    }
}
