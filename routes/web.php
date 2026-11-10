<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\DemoRole;
use Illuminate\Support\Facades\Route;

/* Publik */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{slug}', [CatalogController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')->name('catalog.show');
Route::get('/cek-ketersediaan', [AvailabilityController::class, 'show'])->name('availability');

/* Halaman informasi */
Route::get('/asisten-ai', [PageController::class, 'ai'])->name('ai');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/panduan', [PageController::class, 'guide'])->name('guide');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/artikel', [PageController::class, 'articles'])->name('articles.index');

/* SEO */
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

/* Autentikasi (SIMULASI sampai Fase 5). throttle = pembatasan percobaan (anti brute force). */
Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
Route::post('/auth/google/demo', [AuthController::class, 'googleDemo'])->middleware('throttle:6,1')->name('auth.google.demo');
Route::post('/auth/demo', [AuthController::class, 'demoLogin'])->middleware('throttle:10,1')->name('auth.demo');
Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

/* Pelanggan (butuh login; SIMULASI sampai Fase 5) */
Route::middleware(DemoRole::class.':customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/penyewaan-aktif', [CustomerController::class, 'rentals'])->name('dashboard.rentals');
    Route::get('/dashboard/riwayat', [CustomerController::class, 'history'])->name('dashboard.history');
    Route::get('/dashboard/antrean', [CustomerController::class, 'waitlist'])->name('dashboard.waitlist');
    Route::post('/antrean', [CustomerController::class, 'waitlistStore'])->name('waitlist.store');
    Route::get('/dashboard/pengembalian', [CustomerController::class, 'returns'])->name('dashboard.returns');
    Route::get('/dashboard/profil', [CustomerController::class, 'profile'])->name('dashboard.profile');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerController::class, 'checkoutStore'])->name('checkout.store');
    Route::get('/invoice/{number}', [CustomerController::class, 'invoice'])
        ->where('number', '[A-Za-z0-9-]+')->name('invoice.show');
});

/* Admin (SIMULASI sampai Fase 5: ganti dengan auth + otorisasi role sungguhan) */
Route::middleware(DemoRole::class.':admin')->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/new-orders', 'newOrders')->name('orders.new');
    Route::get('/ongoing-rentals', 'ongoing')->name('rentals.ongoing');
    Route::get('/verifikasi-pembayaran', 'payments')->name('payments');
    Route::get('/qr-scanner', 'scanner')->name('scanner');
    Route::get('/verifikasi-pengambilan', 'pickups')->name('pickups');
    Route::get('/pengembalian', 'returns')->name('returns');
    Route::get('/pemeriksaan-kondisi', 'inspections')->name('inspections');
    Route::get('/denda', 'fines')->name('fines');
    Route::get('/produk', 'products')->name('products');
    Route::get('/inventaris', 'inventory')->name('inventory');
    Route::get('/pelanggan', 'customers')->name('customers');
    Route::get('/antrean-rental', 'waitlist')->name('waitlist');
    Route::get('/laporan-keuangan', 'reports')->name('reports');
});

/*
| Fase 2 — halaman referensi design system. Hanya di environment local.
*/
if (app()->environment('local')) {
    Route::view('/design-system', 'design-system')->name('design-system');
}
