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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

Route::get('/woocommerce', [App\Http\Controllers\WooCommerceController::class, 'wc']);

// Users

Route::get('/users', [App\Http\Controllers\UserController::class, 'directory'])->middleware('admin');

Route::post('/adduser', [App\Http\Controllers\UserController::class, 'store'])->middleware('admin');

Route::get('/user/{id}', [App\Http\Controllers\UserController::class, 'show'])->middleware('auth');

Route::put('/edituser/{id}', [App\Http\Controllers\UserController::class, 'update'])->middleware('auth');

Route::delete('/deleteuser/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('admin');


// Warehouses

Route::get('/warehouse/list', [App\Http\Controllers\WarehouseController::class, 'list']);

Route::get('/warehouse/create', function () { return view('createWarehouse'); });

Route::post('/warehouse/create', [App\Http\Controllers\WarehouseController::class, 'store']);

Route::post('/warehouse/delete/{id}', [App\Http\Controllers\WarehouseController::class, 'destroy']);

// Products

Route::get('/newProducts', [App\Http\Controllers\ProductsController::class, 'newProducts']);

Route::post('/newProducts/store', [App\Http\Controllers\ProductsController::class, 'store']);

Route::post('prepare/{id}', [App\Http\Controllers\ProductsController::class, 'prepareOrder']);

Route::post('prepare/{id}/changeStatus', [App\Http\Controllers\ProductsController::class, 'prepareOrder']);
// Stock

Route::get('/stock/products', [App\Http\Controllers\ProductsController::class, 'list']);

Route::get('/stock/products/{woo_id}', [App\Http\Controllers\ProductsController::class, 'show']);

Route::get('/stock/syncWoocommerce', [App\Http\Controllers\ProductsController::class, 'syncWoocommerce'])->name('syncWoocommerce');

