<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesPageController; // Jangan lupa di-import

// --- ENDPOINT PUBLIK ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// --- ENDPOINT PRIVAT (Wajib Login) ---
Route::middleware('auth:sanctum')->group(function () {
    // Info User & Logout
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // CRUD Sales Page & AI Generator
    Route::get('/sales-pages', [SalesPageController::class, 'index']);      // Ambil semua history
    Route::post('/sales-pages', [SalesPageController::class, 'store']);     // Generate AI & Simpan
    Route::get('/sales-pages/{id}', [SalesPageController::class, 'show']);  // Ambil detail 1 page
    Route::delete('/sales-pages/{id}', [SalesPageController::class, 'destroy']); // Hapus page
});