<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;

// Проверочный маршрут
Route::get('/ping', function () {
    return response()->json(['message' => 'API работает!']);
});

// Маршруты для баланса
Route::post('/deposit', [BalanceController::class, 'deposit']);
Route::post('/withdraw', [BalanceController::class, 'withdraw']);
Route::post('/transfer', [BalanceController::class, 'transfer']);
Route::get('/balance/{user_id}', [BalanceController::class, 'balance']);
