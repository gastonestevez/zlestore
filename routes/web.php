<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\OrderController;

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

Auth::routes(['register' => false]);

Route::get('/', [HomeController::class, 'index']);

Route::get('/test', [HomeController::class, 'index2']);

Route::view('/index3', 'index3');

// Orders

Route::get('/orders', [OrderController::class, 'orders'])->name('home')->middleware('auth');

Route::get('/woocommerce', [WooCommerceController::class, 'wc']);

Route::get('/prepare/{id}', [OrderController::class, 'prepare'])->middleware('auth');

Route::post('/storeOrder/{id}', [OrderController::class, 'storeOrder'])->middleware('auth');


// Users

Route::get('/users', [UserController::class, 'directory'])->middleware('admin');

Route::post('/adduser', [UserController::class, 'store'])->middleware('admin');

Route::get('/user', [UserController::class, 'show'])->middleware('auth');

Route::get('/user/{id}', [UserController::class, 'showAdmin'])->middleware('admin');

Route::put('/edituser/{id}', [UserController::class, 'update'])->middleware('auth');

Route::delete('/deleteuser/{id}', [UserController::class, 'destroy'])->middleware('admin');


// Warehouses

Route::get('/warehouse/list', [WarehouseController::class, 'list'])->middleware('auth');

Route::get('/warehouse/{id}/products', [WarehouseController::class, 'products'])->middleware('auth');

Route::get('/warehouse/new', [WarehouseController::class, 'new'])->name('newWarehouse')->middleware('auth');

Route::post('/warehouse/store', [WarehouseController::class, 'store'])->middleware('auth');

Route::put('/warehouse/update/{id}', [WarehouseController::class, 'update'])->middleware('auth');

Route::delete('/warehouse/delete/{id}', [WarehouseController::class, 'destroy'])->middleware('admin');

// Products

Route::post('prepare/{id}', [ProductsController::class, 'prepareOrder'])->middleware('auth');

Route::post('prepare/{id}/changeStatus', [ProductsController::class, 'prepareOrder'])->middleware('auth');

Route::put('/updatingBoxes/{id}', [ProductsController::class, 'updatingBoxes'])->middleware('admin');

Route::put('/updatingUnits/{id}', [ProductsController::class, 'updatingUnits'])->middleware('admin');

Route::get('/products/loadcsv', [ProductsController::class, 'loadcsv'])->middleware(('auth'));

Route::post('/products/loadcsv', [ProductsController::class, 'storecsv'])->middleware(('auth'))->name('csv-import');


// Stock

Route::get('/products/stock', [ProductsController::class, 'list'])->name('stockList')->middleware('auth');

Route::get('/product/{id}/stock', [ProductsController::class, 'show'])->middleware('auth');

Route::get('/products/syncWoocommerce', [ProductsController::class, 'syncWoocommerce'])->name('syncWoocommerce')->middleware('auth');

Route::put('/transferingUnits/{id}', [WarehouseController::class, 'transferingUnits'])->middleware('employee');

Route::put('/transferingBoxes/{id}', [WarehouseController::class, 'transferingBoxes'])->middleware('employee');


