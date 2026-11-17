<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUnitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
// Klik detail produk memerlukan akun. Middleware auth menyimpan url.intended agar kembali setelah login.
Route::get('/produk/{slug}', [CatalogController::class, 'show'])->where('slug', '[a-z0-9-]+')->middleware('auth')->name('catalog.show');
Route::get('/cek-ketersediaan', [AvailabilityController::class, 'show'])->name('availability');
Route::get('/asisten-ai', [PageController::class, 'ai'])->name('ai');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/panduan', [PageController::class, 'guide'])->name('guide');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/artikel', [PageController::class, 'articles'])->name('articles.index');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Invoice dapat dibuka pemilik pesanan atau admin; controller memeriksa otorisasi pemilik/admin.
Route::get('/invoice/{number}', [CustomerController::class, 'invoice'])
    ->middleware('auth')->where('number', '[A-Za-z0-9-]+')->name('invoice.show');

Route::middleware(['auth', EnsureRole::class.':customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/penyewaan-aktif', [CustomerController::class, 'rentals'])->name('dashboard.rentals');
    Route::get('/dashboard/riwayat', [CustomerController::class, 'history'])->name('dashboard.history');
    Route::get('/dashboard/antrean', [CustomerController::class, 'waitlist'])->name('dashboard.waitlist');
    Route::post('/antrean', [CustomerController::class, 'waitlistStore'])->name('waitlist.store');
    Route::get('/dashboard/pengembalian', [CustomerController::class, 'returns'])->name('dashboard.returns');
    Route::get('/dashboard/profil', [CustomerController::class, 'profile'])->name('dashboard.profile');
    Route::put('/dashboard/profil', [CustomerController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::put('/dashboard/kata-sandi', [CustomerController::class, 'updatePassword'])->middleware('throttle:6,1')->name('dashboard.password.update');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerController::class, 'checkoutStore'])->name('checkout.store');
});

Route::middleware(['auth', EnsureRole::class.':admin'])->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/new-orders', 'newOrders')->name('orders.new');
    Route::get('/ongoing-rentals', 'ongoing')->name('rentals.ongoing');
    Route::get('/verifikasi-pembayaran', 'payments')->name('payments');
    Route::post('/pembayaran/{payment}/verifikasi', 'verifyPayment')->name('payments.verify');
    Route::get('/qr-scanner', 'scanner')->name('scanner');
    Route::post('/qr-scanner', 'scannerLookup')->name('scanner.lookup');
    Route::post('/pengambilan/{order}/konfirmasi', 'confirmPickup')->name('pickups.confirm');
    Route::get('/verifikasi-pengambilan', 'pickups')->name('pickups');
    Route::post('/pengembalian/{order}/terima', 'receiveReturn')->name('returns.receive');
    Route::get('/pengembalian', 'returns')->name('returns');
    Route::get('/pemeriksaan-kondisi', 'inspections')->name('inspections');
    Route::post('/pemeriksaan-kondisi/{order}', 'storeInspection')->name('inspections.store');
    Route::get('/bukti-pemeriksaan/{inspection}', 'inspectionEvidence')->name('inspections.evidence');
    Route::get('/denda', 'fines')->name('fines');
    Route::post('/denda', 'storeFine')->name('fines.store');
    Route::post('/denda/{fine}/penilaian', 'assessFine')->name('fines.assess');
    Route::post('/denda/{fine}/bayar', 'markFinePaid')->name('fines.paid');
    Route::post('/denda/{fine}/hapus-tagihan', 'waiveFine')->name('fines.waive');
    Route::get('/pelanggan', 'customers')->name('customers');
    Route::post('/pelanggan/{user}/status', 'toggleCustomer')->name('customers.toggle');
    Route::get('/antrean-rental', 'waitlist')->name('waitlist');
    Route::post('/antrean-rental/{entry}/status', 'waitlistStatus')->name('waitlist.status');
    Route::get('/laporan-keuangan', 'reports')->name('reports');
    Route::get('/produk', [\App\Http\Controllers\AdminProductController::class, 'index'])->name('products');
    Route::get('/produk/baru', [\App\Http\Controllers\AdminProductController::class, 'create'])->name('products.create');
    Route::post('/produk', [\App\Http\Controllers\AdminProductController::class, 'store'])->name('products.store');
    Route::get('/produk/{product}/ubah', [\App\Http\Controllers\AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/produk/{product}', [\App\Http\Controllers\AdminProductController::class, 'update'])->name('products.update');
    Route::post('/produk/{product}/status', [\App\Http\Controllers\AdminProductController::class, 'toggle'])->name('products.toggle');
    Route::get('/pengaturan', [\App\Http\Controllers\AdminSettingsController::class, 'show'])->name('settings');
    Route::put('/pengaturan', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('settings.update');
    Route::get('/inventaris', [\App\Http\Controllers\AdminUnitController::class, 'index'])->name('inventory');
    Route::get('/inventaris/baru', [\App\Http\Controllers\AdminUnitController::class, 'create'])->name('units.create');
    Route::post('/inventaris', [\App\Http\Controllers\AdminUnitController::class, 'store'])->name('units.store');
    Route::get('/inventaris/{unit}/ubah', [\App\Http\Controllers\AdminUnitController::class, 'edit'])->name('units.edit');
    Route::put('/inventaris/{unit}', [\App\Http\Controllers\AdminUnitController::class, 'update'])->name('units.update');
});

if (app()->environment('local')) Route::view('/design-system', 'design-system')->name('design-system');
