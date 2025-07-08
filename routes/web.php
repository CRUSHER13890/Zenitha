<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\PengeluaranController;

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [HomeController::class, 'index'])->name('home.index');
Route::get('/home/products', [HomeController::class, 'products'])->name('home.products');
Route::get('/home/reservations', [HomeController::class, 'reservations'])->name('home.reservations');
Route::get('/home/add-reservasi/{id}', [HomeController::class, 'addReservation'])->name('home.add-reservasi');
Route::post('/home/store-reservasi/', [HomeController::class, 'storeReservation'])->name('home.store-reservasi');
Route::get('/home/cart', [HomeController::class, 'cart'])->name('home.cart');
Route::get('/home/add-cart/{id}', [HomeController::class, 'addCart'])->name('home.add-cart');
Route::get('/home/remove-cart/{id}', [HomeController::class, 'remove'])->name('home.remove-cart');
Route::get('/home/decrement-cart/{id}', [HomeController::class, 'decrement'])->name('home.decrement-cart');
Route::get('/home/increment-cart/{id}', [HomeController::class, 'increment'])->name('home.increment-cart');
Route::get('/home/checkout', [HomeController::class, 'checkout'])->name('home.checkout');
Route::get('/home/history', [HomeController::class, 'history'])->name('home.history');
Route::get('/home/history-reservation', [HomeController::class, 'historyReservation'])->name('home.history-reservation');
Route::get('/snap-token/{id}', [HomeController::class, 'getSnapToken'])->name('snap-token');
Route::get('/check-status', [HomeController::class, 'checkStatus'])->name('check-status');
Route::post('/home/get-diskon', [HomeController::class, 'getDiskon'])->name('home.getDiskon');
Route::get('/debug-snap-token/{id}', [HomeController::class, 'debugSnapToken'])->name('debug-snap-token');


Route::middleware('auth', 'admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MASTER DATA START
    // Chart Of Account
    Route::get('/coa/destroy/{id}', [CoaController::class, 'destroy'])->name('coa.destroy');
    Route::resource('coa', CoaController::class);

    // Pelanggan
    Route::get('/pelanggan/destroy/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::resource('pelanggan', PelangganController::class);

    // Pegawai
    Route::get('/pegawai/destroy/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
    Route::resource('pegawai', PegawaiController::class);

    // Vendor
    Route::get('/vendor/destroy/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');
    Route::resource('vendor', VendorController::class);

    // Barang
    Route::get('/barang/destroy/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::resource('barang', BarangController::class);
    Route::get('barang/toggle/{id}', [BarangController::class, 'toggleStatus'])->name('barang.toggle');

    // Diskon
    Route::get('/diskon/destroy/{id}', [DiskonController::class, 'destroy'])->name('diskon.destroy');
    Route::resource('diskon', DiskonController::class);
    // Layanan
    Route::get('/layanan/destroy/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    Route::resource('layanan', LayananController::class);

    // Jadwal
    // Route::get('/jadwal/destroy/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    // Route::resource('jadwal', JadwalController::class);
    // MASTER DATA END

    // TRANSAKSI START
    // Reservasi
    Route::get('/reservasi/destroy/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy');
    Route::get('/reservasi/approve/{id}', [ReservasiController::class, 'approve'])->name('reservasi.approve');
    Route::get('/reservasi/cancel/{id}', [ReservasiController::class, 'cancel'])->name('reservasi.cancel');
    Route::resource('reservasi', ReservasiController::class);

    // Pembelian
    Route::get('/pembelian/destroy/{id}', [PembelianController::class, 'destroy'])->name('pembelian.destroy');
    Route::resource('pembelian', PembelianController::class);
    Route::get('/pembelian/detail/{id}', [PembelianController::class, 'detail'])->name('pembelian.detail');
    Route::post('/pembelian/storedetail', [PembelianController::class, 'storedetail'])->name('pembelian.storedetail');
    Route::get('/pembelian/destroydetail/{id}', [PembelianController::class, 'destroydetail'])->name('pembelian.destroydetail');
    Route::get('/pembelian/finish/{id}', [PembelianController::class, 'finish'])->name('pembelian.finish');

    // Penjualan
    Route::get('/penjualan/destroy/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/detail/{id}', [PenjualanController::class, 'detail'])->name('penjualan.detail');
    Route::post('/penjualan/storedetail', [PenjualanController::class, 'storedetail'])->name('penjualan.storedetail');
    Route::get('/penjualan/destroydetail/{id}', [PenjualanController::class, 'destroydetail'])->name('penjualan.destroydetail');
    Route::post('/penjualan/get-diskon', [PenjualanController::class, 'getDiskon'])->name('penjualan.getDiskon');

    // Pendapatan
    Route::get('/pendapatan/destroy/{id}', [PendapatanController::class, 'destroy'])->name('pendapatan.destroy');
    Route::resource('pendapatan', PendapatanController::class);
    Route::get('/pendapatan/detail/{id}', [PendapatanController::class, 'detail'])->name('pendapatan.detail');
    Route::post('/pendapatan/storedetail', [PendapatanController::class, 'storedetail'])->name('pendapatan.storedetail');
    Route::get('/pendapatan/destroydetail/{id}', [PendapatanController::class, 'destroydetail'])->name('pendapatan.destroydetail');
    Route::post('/pendapatan/get-diskon', [PendapatanController::class, 'getDiskonPendapatan'])->name('pendapatan.getDiskon');

    // Pengeluaran
    Route::get('/pengeluaran/destroy/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
    Route::resource('pengeluaran', PengeluaranController::class);

    // Modal
    Route::get('/modal/destroy/{id}', [ModalController::class, 'destroy'])->name('modal.destroy');
    Route::resource('modal', ModalController::class);

    // TRANSAKSI END

    // LAPORAN START
    Route::controller(LaporanController::class)->group(function () {
        Route::get('laporan/jurnalumum', 'jurnalumum')->name('laporan.jurnalumum');
        Route::get('laporan/viewdatajurnalumum/{periode}', 'viewdatajurnalumum');
        Route::get('laporan/bukubesar', 'bukubesar')->name('laporan.bukubesar');
        Route::get('laporan/viewdatabukubesar/{periode}/{akun}', 'viewdatabukubesar');
        Route::get('laporan/pembelian', 'pembelian')->name('laporan.pembelian');
        Route::get('laporan/viewdatapembelian/{periode}', 'viewdatapembelian');
        Route::get('laporan/penjualan', 'penjualan')->name('laporan.penjualan');
        Route::get('laporan/viewdatapenjualan/{periode}', 'viewdatapenjualan');
        Route::get('laporan/labarugi', 'labarugi')->name('laporan.labarugi');
        Route::get('laporan/viewdatalabarugi/{periode}', 'viewdatalabarugi');
        Route::get('/laporan/kartustok', [LaporanController::class, 'kartustok'])->name('laporan.kartustok');
        Route::get('/laporan/viewdatakartustok/{periode}/{id_barang?}', [LaporanController::class, 'viewdatakartustok'])->name('laporan.viewdatakartustok');
        Route::get('laporan/pengeluarankas', 'pengeluarankas')->name('laporan.pengeluarankas');
        Route::get('laporan/viewdatapengeluarankas/{periode}', 'viewdatapengeluarankas');
        Route::get('/laporan/laporanfifo', [LaporanController::class, 'laporanfifo']);
        Route::get('/laporan/viewdatalaporanfifo/{periode}', [LaporanController::class, 'viewdatalaporanfifo']);
    });
    // LAPORAN END
});
// Tambahkan routes berikut untuk notifikasi (letakkan di dalam group middleware auth)

Route::middleware('auth')->group(function () {
    // Routes notifikasi
    Route::get('/notifications', [NotifikasiController::class, 'getNotifications'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotifikasiController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Routes lainnya...
});

// Admin notification routes
Route::prefix('admin')->group(function () {
    Route::get('/notifications', [NotifikasiController::class, 'getAdminNotifications']);
    Route::post('/notifications/mark-as-read', [NotifikasiController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotifikasiController::class, 'markAllAsRead']);
});

require __DIR__ . '/auth.php';
