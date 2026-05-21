<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;


use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes - USER AREA
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('event')->group(function () {
    Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
    Route::get('/{id?}', [EventController::class, 'show'])->name('events.show');
});

Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');


/*
|--------------------------------------------------------------------------
| Web Routes - ADMIN AREA (Grup Terpusat)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    
    Route::resource('events', EventAdminController::class);
    
    
    Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');

    
    
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

});



Route::get('/admin/partners', [PartnerController::class, 'index'])->name('partners.index');
Route::post('/admin/partners/store', [PartnerController::class, 'store'])->name('partners.store');
Route::get('/admin/partners/{id}/edit', [PartnerController::class, 'edit'])->name('partners.edit');
Route::put('/admin/partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
Route::delete('/admin/partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');