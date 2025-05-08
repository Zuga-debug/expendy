<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    
public function summary(Request $request)
{
    $user = $request->user();
    return response()->json([
        'total_expenses' => $user->expenses()->whereMonth('created_at', now()->month)->sum('amount'),
        'categories_count' => $user->categories()->count(),
        'latest_expenses' => $user->expenses()->latest()->take(5)->get(),
        'budget_limit' => $user->budget_limit, // if defined
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
    return response()->json([
        'budget_limit' => $user->budget_limit,
        'total_expenses' => $user->expenses()->whereMonth('created_at', now()->month)->sum('amount'),
        'remaining_budget' => $user->budget_limit - $user->expenses()->whereMonth('created_at', now()->month)->sum('amount'),
    ]);

}



}
