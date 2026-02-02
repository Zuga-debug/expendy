<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Budget;

class BudgetController extends Controller
{
    public function current(Request $request)
    {
        $budget = Budget::where('user_id', $request->user()->id)
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        return response()->json([
            'data' => $budget
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $budget = Budget::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'month' => now()->month,
                'year' => now()->year,
            ],
            ['amount' => $data['amount']]
        );

        return response()->json([
            'message' => 'Budget saved',
            'data' => $budget
        ]);
    }


public function saveBudget(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:0',
    ]);

    $budget = Budget::updateOrCreate(
        [
            'user_id' => $request->user()->id,
            'month' => now()->month,
            'year' => now()->year,
        ],
        [
            'amount' => $request->amount,
        ]
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Budget saved successfully',
        'data' => $budget
    ]);
}

}

