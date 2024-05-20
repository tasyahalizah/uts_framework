<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Panggil ProductController Sebagai Object
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UserCT;

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::middleware(['jwt-auth'])->group(function () {
    //Buat route untuk menambahkan data produk
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'showAll']);
    Route::get('products/{id}', [ProductController::class, 'showById']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'delete']);
    Route::get('products/search/name={name}', [ProductController::class, 'showByName']);
});

Route::middleware(['jwt-auth', 'admin'])->group(function () {
    // CRUD Category hanya untuk admin
    Route::post('categories', [CategoriesController::class, 'store']);
    Route::get('categories', [CategoriesController::class, 'showAll']);
    Route::get('categories/{id}', [CategoriesController::class, 'showById']);
    Route::put('categories/{id}', [CategoriesController::class, 'update']);
    Route::delete('categories/{id}', [CategoriesController::class, 'delete']);
    Route::get('categories/search/name={name}', [CategoriesController::class, 'showByName']);
});