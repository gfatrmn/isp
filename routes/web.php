<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PPPoEController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

// Proteksi Autentikasi Laravel Breeze

// Router Mikrotik & Realtime Monitoring
Route::get('/mikrotik', [MikrotikController::class, 'index'])->name('mikrotik.index');
Route::post('/mikrotik', [MikrotikController::class, 'store'])->name('mikrotik.store');
Route::get('/mikrotik/test/{id}', [MikrotikController::class, 'testConnect'])->name('mikrotik.test');
Route::get('/mikrotik/{id}/monitor', [MikrotikController::class, 'monitor'])->name('mikrotik.monitor');
Route::get('/mikrotik/{id}/traffic', [MikrotikController::class, 'getTrafficRealtime'])->name('mikrotik.traffic');
Route::delete('/mikrotik/{id}', [MikrotikController::class, 'destroy'])->name('mikrotik.destroy');

// Master Data Customers (Resource)
Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

// PPPoE Management & Core Pull Sync Mikrotik
Route::get('/pppoe', [PPPoEController::class, 'index'])->name('pppoe.index');
Route::post('/pppoe', [PPPoEController::class, 'store'])->name('pppoe.store');
Route::get('/pppoe/sync/{serverId}', [PPPoEController::class, 'syncFromMikrotik'])->name('pppoe.sync');

// Packages Layanan Internet
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/generate', [InvoiceController::class, 'generateMonthlyInvoices'])->name('invoices.generate');

require __DIR__ . '/auth.php';
