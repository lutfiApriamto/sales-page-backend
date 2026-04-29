<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesPageController; 

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    Route::get('/sales-pages', [SalesPageController::class, 'index']);     
    Route::post('/sales-pages', [SalesPageController::class, 'store']);
    Route::get('/sales-pages/{id}', [SalesPageController::class, 'show']);
    Route::delete('/sales-pages/{id}', [SalesPageController::class, 'destroy']); 
});