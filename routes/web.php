<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukKategoriController;
use App\Http\Controllers\StokReorderController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\PiutangUtangController;
use App\Http\Controllers\PasarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;







Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan');
Route::get('/produk-kategori', [ProdukKategoriController::class, 'index'])->name('produk_kategori');
Route::get('/stok-reorder', [StokReorderController::class, 'index'])->name('stok_reorder');
Route::get('/cashflow', [CashflowController::class, 'index'])->name('cashflow');
Route::get('/piutang-utang', [PiutangUtangController::class, 'index'])->name('piutang_utang');
Route::get('/pasar', [PasarController::class, 'index'])->name('pasar');
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
Route::post('/pengaturan', [PengaturanController::class, 'save'])->name('pengaturan.save');
Route::post('/pengaturan/users', [PengaturanController::class, 'createUser'])->name('pengaturan.users.create');

