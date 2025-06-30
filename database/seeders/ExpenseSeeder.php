<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        Expense::insert([
            [
                'user_id' => 1,
                'category_id' => 1,
                'amount' => 1500,
                'description' => 'Lunch at cafe',
                'created_at' => now(),
            ],
            [
                'user_id' => 1,
                'category_id' => 2,
                'amount' => 800,
                'description' => 'Taxi fare',
                'created_at' => now(),
            ],
        ]);
    }
}
// This seeder creates two expense records for the first user (user_id = 1).