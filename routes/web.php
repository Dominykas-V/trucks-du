<?php

use App\Http\Controllers\SubstituteController;
use App\Http\Controllers\TrucksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TrucksController::class, 'index'])->name('home');
Route::post('/post-truck', [TrucksController::class, 'create'])->name('post-truck');
Route::post('/update-truck', [TrucksController::class, 'update'])->name('update-truck');
Route::post('/delete-truck/{unit_number}', [TrucksController::class, 'destroy'])->name('delete-truck');

Route::get('substitute', [SubstituteController::class, 'index'])->name('substitute');
Route::post('substitute/post-sub', [SubstituteController::class, 'create'])->name('post-sub');
Route::post('substitute/update-sub', [SubstituteController::class, 'update'])->name('update-sub');
Route::post('substitute/delete-sub/{id}', [SubstituteController::class, 'destroy'])->name('delete-sub');