<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::get('/', function () {
    return Inertia::render('auth/Login', [
        'canResetPassword' => Route::has('password.request'), 
        'canRegister' => Route::has('register'),             
        'status' => session('status') ?? null,
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {  
    Route::resource('categories', Admin\CategoryController::class);
    Route::controller(Admin\CategoryController::class)->group(function () {
        Route::put('categories/{category}/toggle-status', 'toggleStatus')
            ->name('categories.toggle-status');
    });

    Route::resource('customers', Admin\CustomerController::class);
});

require __DIR__.'/settings.php';
