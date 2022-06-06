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

use App\Http\Controllers\UserController;
use App\Models\Post;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {dd(Post::all());
    $post = Post::forceCreate(['title'=>'Laravel 7', 'slug'=>'laravel-7']);
    dd($post);
    return "<h1>Test</h1><p>Hello world</p>";
});

Route::get('/posts/{post}', function(Post $post) {
    dd($post);
});

Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
Route::get('/usuarios/nuevo', [UserController::class, 'create'])->name('users.create');
Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.delete');
