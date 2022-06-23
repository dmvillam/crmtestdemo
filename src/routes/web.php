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
use App\Models\Plantilla;
use App\Mail\NotificacionMail;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\ReportesController;

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

Route::get(		'/tareas/cron',         	[TareaController::class, 'cron']) 	 ->name('tasks.cron');
Route::get(     '/tareas',                	[TareaController::class, 'index'])   ->name('tasks.index');
Route::post(    '/tareas',                	[TareaController::class, 'store'])   ->name('tasks.store');
Route::get(     '/tareas/{tarea}',         	[TareaController::class, 'show'])    ->name('tasks.show');
Route::get(     '/tareas/{tarea}/editar',	[TareaController::class, 'edit'])    ->name('tasks.edit');
Route::put(     '/tareas/{tarea}',         	[TareaController::class, 'update'])  ->name('tasks.update');
Route::delete(  '/tareas/{tarea}',         	[TareaController::class, 'destroy']) ->name('tasks.delete');

Route::get(     '/plantillas',                		[PlantillaController::class, 'index'])   ->name('templates.index');
Route::post(    '/plantillas',                		[PlantillaController::class, 'store'])   ->name('templates.store');
Route::get(     '/plantillas/{plantilla}',         	[PlantillaController::class, 'show'])    ->name('templates.show');
Route::get(     '/plantillas/{plantilla}/editar',  	[PlantillaController::class, 'edit'])    ->name('templates.edit');
Route::put(     '/plantillas/{plantilla}',         	[PlantillaController::class, 'update'])  ->name('templates.update');
Route::delete(  '/plantillas/{plantilla}',         	[PlantillaController::class, 'destroy']) ->name('templates.delete');
Route::post(	'/plantillas/upload',               [PlantillaController::class, 'upload'])	 ->name('templates.upload');

Route::get(     '/reportes/{id?}/{estado?}/{rango?}',	[ReportesController::class, 'index'])->name('reports.index');

Route::get('/test', function () {
	$plantilla = Plantilla::first();
	Mail::to('malavevillamizar.d@gmail.com')->send(new NotificacionMail($plantilla));
	return $plantilla->descripcion_larga;
});
