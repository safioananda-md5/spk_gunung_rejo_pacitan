<?php

use App\Http\Controllers\AcceptanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\WeightValueController;
use App\Http\Controllers\ProfileIdealController;

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
    // Perangkingan
    Route::get('/perangkingan', [RankingController::class, 'index'])->name('ranking');
    Route::get('/perangkingan/hasil-rangking', [RankingController::class, 'rank'])->name('rank');
    Route::post('/perangkingan/hasil-rangking', [RankingController::class, 'post'])->name('post.rank');
    Route::get('/perangkingan/pemetaan-gap', [RankingController::class, 'gap'])->name('gap');
    Route::get('/perangkingan/pembobotan', [RankingController::class, 'weight'])->name('weight');
    Route::get('/perangkingan/core-secondary-factor', [RankingController::class, 'CSF'])->name('CSF');
    Route::get('/perangkingan/nilai-total', [RankingController::class, 'total'])->name('total');
    // Penerimaan
    Route::get('/penerimaan', [AcceptanceController::class, 'index'])->name('acceptance');
    Route::get('/penerimaan-detail/{date}', [AcceptanceController::class, 'detail'])->name('detail.acceptance');
    Route::delete('/hapuspenerimaan', [AcceptanceController::class, 'delete'])->name('delete.acceptance');
    Route::delete('/hapuspenerimaanall', [AcceptanceController::class, 'deleteall'])->name('deleteall.acceptance');
});

// User Routes
Route::group([
    'prefix' => '/user',
    'as' => 'user.',
    'middleware' => ['auth', 'role:user', 'decrypt:id']
], function () {
    Route::get('/dashboard', function () {
        return redirect(route('user.penerimaan'));
    })->name('dashboard');

    Route::get('/penerimaan', [DashboardController::class, 'index'])->name('penerimaan');
    // Penerimaan
    Route::get('/penerimaan-detail/{date}', [AcceptanceController::class, 'detail'])->name('detail.acceptance');
});

Route::group([
    'prefix' => '/export',
    'as' => 'export.',
    'middleware' => ['auth', 'decrypt:id']
], function () {
    Route::get('/excel/{date}', [ExportController::class, 'excel'])->name('excel');
    Route::get('/pdf/{date}', [ExportController::class, 'pdf'])->name('pdf');
});
