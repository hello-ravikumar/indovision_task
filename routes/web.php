<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HandleFileController;

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

Route::get('/', [HandleFileController::class, 'index'])->name('welcome');
Route::get('/edit/{id}', [HandleFileController::class, 'edit'])->name('edit.content');
Route::post('/edit/{id}', [HandleFileController::class, 'update'])->name('update.content');

Route::post('/process-file', [HandleFileController::class, 'processFile'])->name('process-file');
