<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LogsheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\TutorScheduleController;

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
    Route::get('/tutor/{tutor}/edit', [TutorController::class, 'edit'])->name('tutor.edit');
    Route::put('/tutor/{tutor}', [TutorController::class, 'update'])->name('tutor.update');
    Route::get('/tutor/{tutor}/logsheets', [TutorController::class, 'getUnscheduledLogsheets'])->name('tutor.logsheets');
    
    // Tutor Schedule routes
    Route::get('/tutor/{tutor}/schedules', [TutorScheduleController::class, 'index'])->name('tutor.schedules.index');
    Route::post('/tutor/{tutor}/schedules', [TutorScheduleController::class, 'store'])->name('tutor.schedules.store');
    Route::put('/tutor/{tutor}/schedules/{schedule}', [TutorScheduleController::class, 'update'])->name('tutor.schedules.update');
    Route::delete('/tutor/{tutor}/schedules/{schedule}', [TutorScheduleController::class, 'destroy'])->name('tutor.schedules.destroy');
    Route::get('/tutor/{tutor}/available-sessions', [TutorScheduleController::class, 'getAvailableSessions'])->name('tutor.schedules.available');
    Route::get('/tutor/{tutor}/available-dates', [TutorScheduleController::class, 'getAvailableDates'])->name('tutor.schedules.dates');

    // Ledger routes
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/ledger/budget-options', [LedgerController::class, 'getBudgetOptions'])->name('ledger.budget-options');
    Route::post('/ledger', [LedgerController::class, 'store'])->name('ledger.store');
    Route::get('/ledger/{ledger}/edit', [LedgerController::class, 'edit'])->name('ledger.edit');
    Route::put('/ledger/{ledger}', [LedgerController::class, 'update'])->name('ledger.update');
    Route::delete('/ledger/{ledger}', [LedgerController::class, 'destroy'])->name('ledger.destroy');
});