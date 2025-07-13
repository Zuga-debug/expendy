<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'total_expenses' => $user->expenses()->whereMonth('created_at', now()->month)->sum('amount'),
            'categories_count' => $user->categories()->count(),
            'latest_expenses' => $user->expenses()->latest()->take(5)->get(),
            'budget_limit' => $user->budget_limit,
        ]);
    }

    public function categoryBreakdown(Request $request)
    {
        $user = $request->user();
        return $user->expenses()
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();
    }

    public function monthlyTrends(Request $request)
    {
        $user = $request->user();
        return $user->expenses()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function budgetStatus(Request $request)
    {
        $user = $request->user();
        $monthlyExpenses = $user->expenses()->whereMonth('created_at', now()->month)->sum('amount');

        return response()->json([
            'budget_limit' => $user->budget_limit,
            'total_expenses' => $monthlyExpenses,
            'remaining_budget' => $user->budget_limit - $monthlyExpenses,
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        return $user->expenses()->latest()->paginate(10);
    }

    public function transactionStore(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $expense = new Expense();
        $expense->user_id = $request->user()->id;
        $expense->description = $validated['description'];
        $expense->amount = $validated['amount'];
        $expense->category_id = $validated['category_id'];
        $expense->save();

        return response()->json(['message' => 'Transaction saved successfully', 'data' => $expense], 201);
    }
}
