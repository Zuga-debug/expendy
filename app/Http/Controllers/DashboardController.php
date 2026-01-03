<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Budget;

class DashboardController extends Controller
{
    /**
     * Return dashboard summary (total, categories, recent expenses, etc.)
     */
   public function summary(Request $request)
{
    $user = $request->user();

    $monthlyExpenses = $user->expenses()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->sum('amount');

    $budgetLimit = (float) ($user->budget_limit ?? 0);

    $data = [
        'total_expenses'   => (float) $monthlyExpenses,
        'budget_limit'    => $budgetLimit,
        'remaining_budget'=> max($budgetLimit - $monthlyExpenses, 0),
        'percentage_used' => $budgetLimit > 0
            ? round(($monthlyExpenses / $budgetLimit) * 100, 2)
            : 0,
        'categories_count'=> $user->categories()->count(),
        'latest_expenses' => $user->expenses()
            ->latest()
            ->take(5)
            ->get(['id','description','amount','created_at','category_id']),
    ];

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}


    /**
     * Return category breakdown for charts (sum per category)
     */
    public function categoryBreakdown(Request $request)
    {
        $user = $request->user();

        $breakdown = $user->expenses()
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category->name ?? 'Uncategorized',
                    'total' => (float) $item->total
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Category breakdown fetched successfully.',
            'data' => $breakdown
        ]);
    }

    public function budgetStatus(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated'
        ], 401);
    }

    $budgetLimit = (float) ($user->budget_limit ?? 0);

    $monthlyExpenses = (float) $user->expenses()
        ->whereMonth('created_at', now()->month)
        ->sum('amount');

    return response()->json([
        'status' => 'success',
        'data' => [
            'budget_limit' => $budgetLimit,
            'total_expenses' => $monthlyExpenses,
            'remaining_budget' => max($budgetLimit - $monthlyExpenses, 0),
            'percentage_used' => $budgetLimit > 0
                ? round(($monthlyExpenses / $budgetLimit) * 100, 2)
                : 0,
        ]
    ]);
}

    /**
     * Return monthly expense trends
     */
    public function monthlyTrends(Request $request)
    {
        $user = $request->user();

        $trends = $user->expenses()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'total' => (float) $item->total,
                    'label' => now()->setDate($item->year, $item->month, 1)->format('M Y'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Monthly trends fetched successfully.',
            'data' => $trends
        ]);
    }


    /**
     * Paginated list of user transactions
     */
    public function transactions(Request $request)
    {
        $user = $request->user();

        $transactions = $user->expenses()
            ->with('category:id,name')
            ->latest()
            ->get(['id', 'description', 'amount', 'category_id', 'created_at']);

        return response()->json([
            'status' => 'success',
            'message' => 'Transactions fetched successfully.',
            'data' => $transactions
        ]);
    }



    /**
     * Store a new transaction
     */
    public function transactionStore(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $expense = Expense::create([
            'user_id' => $request->user()->id,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction saved successfully.',
            'data' => $expense
        ], 201);
    }

    public function transactionUpdate(Request $request, $id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $expense->update($validated);

        return response()->json($expense);
    }

    public function transactionDelete(Request $request, $id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $expense->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
