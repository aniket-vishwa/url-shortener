<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:SuperAdmin,Admin'])->group(function () {
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/short-urls', [ShortUrlController::class, 'index'])
        ->middleware('role:SuperAdmin,Admin,Member')
        ->name('short-urls.index');

    Route::post('/short-urls', [ShortUrlController::class, 'store'])
        ->middleware('role:Admin,Member')
        ->name('short-urls.store');
});

Route::get('/invite/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invite/{token}', [InvitationController::class, 'complete'])->name('invitations.complete');
Route::get('/s/{code}', [ShortUrlController::class, 'redirect'])->name('short-urls.redirect');


require __DIR__.'/auth.php';