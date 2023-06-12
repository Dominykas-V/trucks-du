<?php

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
