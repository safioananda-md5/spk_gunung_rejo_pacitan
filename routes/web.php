<?php

use App\Http\Controllers\AcceptanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'post'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::group([
    'prefix' => '/admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'role:admin', 'decrypt:id']
], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Kriteria
    Route::get('/kriteria', [CriteriaController::class, 'index'])->name('criteria');
    Route::get('/tambah-kriteria', [CriteriaController::class, 'create'])->name('create.criteria');
    Route::post('/tambah-kriteria', [CriteriaController::class, 'post'])->name('post.criteria');
    Route::get('/edit-kriteria/{id}', [CriteriaController::class, 'edit'])->name('edit.criteria');
    Route::post('/update-kriteria/{id}', [CriteriaController::class, 'update'])->name('update.criteria');
    Route::post('/delete-kriteria/{id}', [CriteriaController::class, 'delete'])->name('delete.criteria');
    // Input Data
    Route::get('/input-data', [DataController::class, 'index'])->name('input.data');
    Route::post('/input-data', [DataController::class, 'post'])->name('post.data');
    Route::delete('/delete-data', [DataController::class, 'delete'])->name('delete.data');
    Route::delete('/delete-all-data', [DataController::class, 'deleteAll'])->name('delete.all.data');
    // Penerimaan
    Route::get('/penerimaan', [AcceptanceController::class, 'index'])->name('acceptance');
    Route::post('/penerimaan', [AcceptanceController::class, 'post'])->name('post.acceptance');
});
