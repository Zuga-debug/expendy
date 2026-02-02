<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;

// === Public Auth Routes ===
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// === Protected Routes ===
Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::post('/profile/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // Dashboard summary + charts
    Route::get('/dashboard', [DashboardController::class, 'summary']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/expenses', [DashboardController::class, 'expenses']);
    Route::get('/dashboard/incomes', [DashboardController::class, 'incomes']);
    Route::get('/dashboard/budget', [DashboardController::class, 'budget']);
    Route::get('/dashboard/category-breakdown', [DashboardController::class, 'categoryBreakdown']);
    Route::get('/dashboard/monthly-trends', [DashboardController::class, 'monthlyTrends']);
    Route::get('/dashboard/budget-status', [DashboardController::class, 'budgetStatus']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);

    // Transactions
    Route::get('/dashboard/transactions', [DashboardController::class, 'transactions']);
    Route::get('/dashboard/transactions/{id}', [DashboardController::class, 'transactionDetails']);
    Route::post('/dashboard/transactions', [DashboardController::class, 'transactionStore']);
    Route::put('/dashboard/transactions/{id}', [DashboardController::class, 'transactionUpdate']);
    Route::delete('/dashboard/transactions/{id}', [DashboardController::class, 'transactionDelete']);
    //BUDGET
    Route::get('/budget/current', [BudgetController::class, 'current']);
    Route::post('/budget', [BudgetController::class, 'store']);
    Route::post('/dashboard/budget', [DashboardController::class, 'storeBudget']);
    Route::put('/dashboard/budget/{budget}', [DashboardController::class, 'updateBudget']);
    Route::delete('/dashboard/budget/{budget}', [DashboardController::class, 'deleteBudget']);

});
