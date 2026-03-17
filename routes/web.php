<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── User Routes ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('tickets.index'));
    Route::get('/tickets',              [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create',       [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',             [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}',     [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
});

// ─── Admin / Agent Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,agent'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                          [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets',                   [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{ticket}',          [AdminController::class, 'showTicket'])->name('tickets.show');
    Route::put('/tickets/{ticket}',          [AdminController::class, 'updateTicket'])->name('tickets.update');
    Route::post('/tickets/{ticket}/reply',   [AdminController::class, 'replyTicket'])->name('tickets.reply');
    Route::get('/users',                     [AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}',              [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/categories',                [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories',               [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}',  [AdminController::class, 'deleteCategory'])->name('categories.delete');
});
