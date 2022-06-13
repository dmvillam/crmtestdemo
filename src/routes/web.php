<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpresaController;
use App\Models\Post;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get(     '/usuarios',                [UserController::class, 'index'])   ->name('users.index');
Route::post(    '/usuarios',                [UserController::class, 'store'])   ->name('users.store');
Route::get(     '/usuarios/{user}',         [UserController::class, 'show'])    ->name('users.show');
Route::get(     '/usuarios/{user}/editar',  [UserController::class, 'edit'])    ->name('users.edit');
Route::put(     '/usuarios/{user}',         [UserController::class, 'update'])  ->name('users.update');
Route::delete(  '/usuarios/{user}',         [UserController::class, 'destroy']) ->name('users.delete');

Route::get(     '/empresas',                    [EmpresaController::class, 'index'])   ->name('companies.index');
Route::post(    '/empresas',                    [EmpresaController::class, 'store'])   ->name('companies.store');
Route::get(     '/empresas/{empresa}',          [EmpresaController::class, 'show'])    ->name('companies.show');
Route::get(     '/empresas/{empresa}/editar',   [EmpresaController::class, 'edit'])    ->name('companies.edit');
Route::put(     '/empresas/{empresa}',          [EmpresaController::class, 'update'])  ->name('companies.update');
Route::delete(  '/empresas/{empresa}',          [EmpresaController::class, 'destroy']) ->name('companies.delete');