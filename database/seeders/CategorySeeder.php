<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assume the first user exists (user_id = 1)
        Category::insert([
            ['name' => 'Food', 'user_id' => 1],
            ['name' => 'Transport', 'user_id' => 1],
            ['name' => 'Utilities', 'user_id' => 1],
        ]);
    }
}
