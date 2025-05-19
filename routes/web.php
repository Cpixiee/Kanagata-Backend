<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LogsheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\LedgerController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Insight routes
    Route::get('/insight', [LogsheetController::class, 'insight'])->name('insight');
    Route::get('/logsheets/chart-data', [LogsheetController::class, 'getChartData'])->name('logsheet.chart-data');
    
    // Projects routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    
    // Logsheets routes
    Route::get('/logsheets', [LogsheetController::class, 'index'])->name('logsheet.index');
    Route::post('/logsheets', [LogsheetController::class, 'store'])->name('logsheet.store');
    Route::get('/logsheets/{logsheet}/edit', [LogsheetController::class, 'edit'])->name('logsheet.edit');
    Route::put('/logsheets/{logsheet}', [LogsheetController::class, 'update'])->name('logsheet.update');
    Route::delete('/logsheets/{logsheet}', [LogsheetController::class, 'destroy'])->name('logsheet.destroy');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/customer', [CustomerController::class, 'store']);

    // Tutor routes
    Route::get('/tutor', [TutorController::class, 'index'])->name('tutor.index');
    
    // Ledger routes
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
});