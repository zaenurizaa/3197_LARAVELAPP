<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

use App\Http\Controllers\Admin\AuthController; 
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sisi Publik / Pengguna
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('event')->group(function () {
    // Sesuai Modul 9.4.6: Letakkan rute statis di atas rute wildcard agar tidak bentrok
    Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
    
    // DISESUAIKAN MODUL 9.4.6: Parameter diubah dari /{id} menjadi /{event}
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
});

Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');

// Shortcut redirect login umum ke halaman login admin
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| Admin Routes - Dengan Proteksi Middleware Autentikasi
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Autentikasi Admin
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Grouping Route Admin (Hanya bisa diakses jika sudah login & punya role admin)
    Route::middleware(['auth', 'admin'])->group(function () {
        
        // Dashboard Route
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Event CRUD Routes (Menggunakan Resource Controller bawaan Laravel)
        Route::resource('events', EventAdminController::class);
        
        // Transaction & Reports Route
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');

        // Category CRUD Routes 
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Partner CRUD Routes
        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::post('/partners/store', [PartnerController::class, 'store'])->name('partners.store');
        Route::get('/partners/{id}/edit', [PartnerController::class, 'edit'])->name('partners.edit'); 
        Route::put('/partners/{id}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    });
});