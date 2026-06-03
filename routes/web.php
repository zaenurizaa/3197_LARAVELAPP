<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

use App\Http\Controllers\Admin\AuthController; 
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CategoryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('event')->group(function () {
    Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
    Route::get('/{id?}', [EventController::class, 'show'])->name('events.show');
});

Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        
        //dashboard route
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        //event routes
        Route::resource('events', EventAdminController::class);
        
        //transaction route
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');

        //category routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        //partner routes
        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::post('/partners/store', [PartnerController::class, 'store'])->name('partners.store');
        Route::get('/partners/{id}/edit', [PartnerController::class, 'edit'])->name('partners.edit'); 
        Route::put('/partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    });
});