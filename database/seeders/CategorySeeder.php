<?php

// database/seeders/CategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['name' => 'Food', 'user_id' => null],
            ['name' => 'Transport', 'user_id' => null],
            ['name' => 'Utilities', 'user_id' => null],
            ['name' => 'Entertainment', 'user_id' => null],
        ]);
    }
}
