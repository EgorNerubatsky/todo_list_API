<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        User::factory()->create([
            'email' => 'test1@todo.test',
            'password' => Hash::make('12345678'),
        ]);

        User::factory()->create([
            'email' => 'test2@todo.test',
            'password' => Hash::make('12345678'),
        ]);

        User::factory()->create([
            'email' => 'test3@todo.test',
            'password' => Hash::make('12345678'),
        ]);
        User::factory(14)->create();

    }
}
