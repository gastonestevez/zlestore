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

Auth::routes([
        'register' => false
    ]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// Orders

Route::get('/orders', [App\Http\Controllers\HomeController::class, 'orders'])->name('home')->middleware('auth');

Route::get('/woocommerce', [App\Http\Controllers\WooCommerceController::class, 'wc']);

// Users

Route::get('/users', [App\Http\Controllers\UserController::class, 'directory'])->middleware('admin');

Route::post('/adduser', [App\Http\Controllers\UserController::class, 'store'])->middleware('admin');

Route::get('/user/{id}', [App\Http\Controllers\UserController::class, 'show'])->middleware('auth');

Route::put('/edituser/{id}', [App\Http\Controllers\UserController::class, 'update'])->middleware('auth');

Route::delete('/deleteuser/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('admin');


// Warehouses

Route::get('/warehouse/list', [App\Http\Controllers\WarehouseController::class, 'list']);

Route::get('/warehouse/{id}/products', [App\Http\Controllers\WarehouseController::class, 'products']);

Route::get('/warehouse/new', [App\Http\Controllers\WarehouseController::class, 'new']);

Route::post('/warehouse/store', [App\Http\Controllers\WarehouseController::class, 'store']);

Route::put('/warehouse/update/{id}', [App\Http\Controllers\WarehouseController::class, 'update']);

Route::delete('/warehouse/delete/{id}', [App\Http\Controllers\WarehouseController::class, 'destroy']);

// Products

Route::get('/newProducts', [App\Http\Controllers\ProductsController::class, 'newProducts']);

Route::post('/newProducts/store', [App\Http\Controllers\ProductsController::class, 'store']);

Route::post('prepare/{id}', [App\Http\Controllers\ProductsController::class, 'prepareOrder']);

Route::post('prepare/{id}/changeStatus', [App\Http\Controllers\ProductsController::class, 'prepareOrder']);

Route::put('/updatingStock/{id}', [App\Http\Controllers\ProductsController::class, 'updatingStock']);


// Stock

Route::get('/products/stock', [App\Http\Controllers\ProductsController::class, 'list']);

Route::get('/product/{woo_id}/stock', [App\Http\Controllers\ProductsController::class, 'show']);

Route::get('/products/syncWoocommerce', [App\Http\Controllers\ProductsController::class, 'syncWoocommerce'])->name('syncWoocommerce');

