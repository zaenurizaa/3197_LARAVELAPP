<?php

use Illuminate\Support\Facades\Route;

// Controller Sisi Publik
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController; 
use App\Http\Controllers\Auth\GoogleController;

// Controller Sisi Admin & Auth
use App\Http\Controllers\Admin\AuthController; 
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\TenantRegisterController;
use App\Http\Controllers\Admin\TenantApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sisi Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('event')->group(function () {
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
});

Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/my-ticket', [EventController::class, 'ticket'])->name('ticket');
Route::post('/review/store', [CheckoutController::class, 'storeReview'])->name('review.store');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::post('/user/logout', [GoogleController::class, 'logout'])->name('user.logout');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::get('/web-scan/{order_id}', [CheckoutController::class, 'processCheckIn'])->name('scan.process');
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handleWebhook']);

/*
|--------------------------------------------------------------------------
| Pendaftaran Tenant / Organizer (Publik)
|--------------------------------------------------------------------------
*/
Route::get('/register-organizer', [TenantRegisterController::class, 'showRegistrationForm'])->name('tenant.register');
Route::post('/register-organizer', [TenantRegisterController::class, 'register'])->name('register.organizer.store');


/*
|--------------------------------------------------------------------------
| 1. ROUTE KHUSUS SUPERADMIN (Guard: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Auth Superadmin
    Route::get('login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('login', [AuthController::class, 'adminLogin'])->name('login.post');
    Route::post('logout', [AuthController::class, 'adminLogout'])->name('logout');

    // Terproteksi Guard Admin
    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // 🔥 TAMBAHKAN ROUTE EVENT UNTUK SUPERADMIN
        Route::resource('events', EventAdminController::class);

        // Transaksi (Semua transaksi untuk Superadmin)
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        // Tenant Approval
        Route::get('/tenants', [TenantApprovalController::class, 'index'])->name('tenants.index');
        Route::patch('/tenants/{tenant}/approve', [TenantApprovalController::class, 'approve'])->name('tenants.approve');
        Route::patch('/tenants/{tenant}/reject', [TenantApprovalController::class, 'reject'])->name('tenants.reject');
        Route::delete('/tenants/{tenant}', [TenantApprovalController::class, 'destroy'])->name('tenants.destroy');

        // Kategori & Partner Management
        Route::resource('categories', CategoryController::class);
        Route::resource('partners', PartnerController::class);
    });
});


/*
|--------------------------------------------------------------------------
| 2. ROUTE KHUSUS EVENT ORGANIZER / TENANT (Guard: organizer)
|--------------------------------------------------------------------------
*/
Route::prefix('organizer')->name('organizer.')->group(function () {
    
    // Auth Organizer
    Route::get('login', [AuthController::class, 'showOrganizerLogin'])->name('login');
    Route::post('login', [AuthController::class, 'organizerLogin'])->name('login.post');
    Route::post('logout', [AuthController::class, 'organizerLogout'])->name('logout');

    // Terproteksi Guard Organizer
    Route::middleware(['auth:organizer', 'ensure.organizer'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Event Management milik Organizer
        Route::resource('events', EventAdminController::class);
        
        // Transaksi milik Organizer
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        // Scanner Gatekeeper
        Route::get('/scanner', function () {
            return view('admin.scanner');
        })->name('scanner');
    });
});