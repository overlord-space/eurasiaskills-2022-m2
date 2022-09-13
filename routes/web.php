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

Route::redirect('/', '/projects');

Route::middleware('guest')->group(function () {
    Route::view('login', 'pages.login')->name('login');
    Route::post('login', ['App\Http\Controllers\AuthController', 'login']);

    Route::view('register', 'pages.register')->name('register');
    Route::post('register', ['App\Http\Controllers\AuthController', 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('logout', ['App\Http\Controllers\AuthController', 'logout'])->name('logout');

    Route::get('/modules', ['App\Http\Controllers\ModuleController', 'index'])->name('module.list');
    Route::view('/modules/add', 'pages.user.modules.module_add')->name('module.add');
    Route::post('/modules/add', ['App\Http\Controllers\ModuleController', 'batchUpload']);
    Route::get('/modules/download/{module}', ['App\Http\Controllers\ModuleController', 'download'])->name('module.download');
    Route::get('/modules/destroy/{module}', ['App\Http\Controllers\ModuleController', 'destroy'])->name('module.delete');

    Route::get('/projects', ['App\Http\Controllers\ProjectController', 'index'])->name('project.list');
    Route::get('/projects/create', ['App\Http\Controllers\ProjectController', 'create'])->name('project.create');
    Route::post('/projects/create', ['App\Http\Controllers\ProjectController', 'store']);
    Route::get('/projects/destroy/{project}', ['App\Http\Controllers\ProjectController', 'destroy'])->name('project.delete');

    Route::get('/tokens', ['App\Http\Controllers\UserController', 'indexToken'])->name('token.list');
    Route::post('/tokens', ['App\Http\Controllers\UserController', 'storeToken']);
    Route::get('/tokens/delete/{token}', ['App\Http\Controllers\UserController', 'destroyToken'])->name('token.delete');
});
