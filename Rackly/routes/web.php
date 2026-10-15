<?php

use App\Http\Controllers\AdminBilliardTableController;
 use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\BilliardTable;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\HomeController;

// Rute Beranda
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// Rute Riwayat Booking User
Route::get('/booking-history', [BookingController::class, 'history'])
    ->middleware(['auth', 'verified'])
    ->name('booking.history');

Route::middleware('auth')->group(function () {
    // Rute Lanjutkan Pembayaran & Batal dari History
    Route::get('/booking/{booking}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Rute untuk menampilkan form booking, dengan membawa ID meja yang dipilih
    Route::get('/booking/{billiardTable}', [BookingController::class, 'create'])->name('bookings.create');

    // Rute untuk memproses form booking (nanti kita buat fungsinya)
    Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');

    // Rute fallback jika Midtrans melakukan redirect/reload (GET) ke /booking
    Route::get('/booking', function () {
        return redirect()->route('booking.history');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Admin Page
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/tables', AdminBilliardTableController::class);
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->middleware(['auth', IsAdmin::class])
        ->name('admin.dashboard');

    // Rute Pengaturan Admin
    Route::get('/admin/settings', [AdminSettingController::class, 'index'])->name('admin.settings');
    Route::patch('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    // Rute Kelola Pesanan Admin
    Route::get('/admin/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::post('/admin/bookings/{booking}/confirm', [AdminController::class, 'confirmFullPayment'])->name('admin.bookings.confirm');

    // Rute Walk-in (Pesanan Langsung di Kasir)
    Route::get('/admin/walkin/create', [AdminController::class, 'createWalkIn'])->name('admin.walkin.create');
    Route::post('/admin/walkin', [AdminController::class, 'storeWalkIn'])->name('admin.walkin.store');
});

require __DIR__ . '/auth.php';

// Route untuk Webhook Midtrans (Tanpa Middleware Auth)
Route::post('/webhook/midtrans', [BookingController::class, 'webhook'])->name('midtrans.webhook');
