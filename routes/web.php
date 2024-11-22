<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\ProductBatches;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Clients;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Services;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', Roles::class)->name('roles.index');
    Route::get('/users', Users::class)->name('users.index');
    Route::get('/clients', Clients::class)->name('clients.index')
        ->middleware('permission:view_clients');
    Route::get('/settings', Settings::class)->name('settings.index');
    Route::get('/products', Products::class)->name('products.index');
    Route::get('/categories', Categories::class)->name('categories.index');
    Route::get('/batches', ProductBatches::class)->name('admin.batches');
    Route::get('/services', Services::class)->name('admin.services');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
