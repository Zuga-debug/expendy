<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
// This seeder calls the UserSeeder, CategorySeeder, and ExpenseSeeder to populate the database with initial data.
