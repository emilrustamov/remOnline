<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Roles;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Route::get('/users', Users::class)->name('users.index');
    Route::get('/roles', \App\Livewire\Admin\Roles::class)->name('roles.index');
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users.index');
    Route::get('/clients', \App\Livewire\Admin\Clients::class)->name('clients.index')
        ->middleware('permission:view_clients');
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
