<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Symfony\Component\HttpKernel\Profiler\Profile;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

// just now
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// avatar and Profile updateProfile
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
});

Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'summary']);
Route::middleware('auth:sanctum')->get('/dashboard/expenses', [DashboardController::class, 'expenses']);
Route::middleware('auth:sanctum')->get('/dashboard/incomes', [DashboardController::class, 'incomes']);
Route::middleware('auth:sanctum')->get('/dashboard/budget', [DashboardController::class, 'budget']);
Route::middleware('auth:sanctum')->get('/dashboard/summary', [DashboardController::class, 'summary']);
// categoryBreakdown
Route::middleware('auth:sanctum')->get('/dashboard/
category-breakdown', [DashboardController::class, 'categoryBreakdown']);
Route::middleware('auth:sanctum')->get('/dashboard/monthly-trends', [DashboardController::class, 'monthlyTrends']);
// budgetStatus
Route::middleware('auth:sanctum')->get('/dashboard/budget-status', [DashboardController::class, 'budgetStatus']);

// categories
Route::middleware('auth:sanctum')->get('/categories', function (Request $request) {
    return $request->user()->categories()->select('id', 'name')->get();
});
// transaction
Route::middleware('auth:sanctum')->get('/dashboard/transactions', [DashboardController::class, 'transactions']);

Route::middleware('auth:sanctum')->get('/dashboard/transactions/{id}', [DashboardController::class, 'transactionDetails']);
Route::middleware('auth:sanctum')->get('/dashboard/transactions/{id}/edit', [DashboardController::class, 'transactionEdit']);
Route::middleware('auth:sanctum')->post('/dashboard/transactions', [DashboardController::class, 'transactionStore']);

Route::middleware('auth:sanctum')->put('/dashboard/transactions/{id}', [DashboardController::class, 'transactionUpdate']);
Route::middleware('auth:sanctum')->delete('/dashboard/transactions/{id}', [DashboardController::class, 'transactionDelete']);
Route::middleware('auth:sanctum')->get('/dashboard/transactions/{id}/delete', [DashboardController::class, 'transactionDelete']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
});
