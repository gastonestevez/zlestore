<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ConceptController;

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

// WcOrders

Route::get('/wcOrders', [OrderController::class, 'wcOrders'])->name('home')->middleware('auth');

Route::get('/woocommerce', [WooCommerceController::class, 'wc']);

Route::get('/prepare/{id}', [OrderController::class, 'prepare'])->middleware('auth');

Route::post('/storeWcOrder/{id}', [OrderController::class, 'storeWcOrder'])->middleware('auth');

// Orders - Feature - sales-system

Route::get('/createOrder', [orderController::class, 'createOrder'])->name('createOrder')->middleware('auth');

Route::get('/orderPreview/{id}', [OrderController::class, 'orderPreview'])->name('orderPreview')->middleware('auth');

Route::post('/addProductToOrder', [OrderController::class, 'addProductToOrder'])->middleware('auth');

Route::delete('/removeProduct/{id}', [OrderController::class, 'removeProduct'])->middleware('auth');

Route::post('/orderToPending/{id}', [OrderController::class, 'orderToPending'])->middleware('auth');


// Users

Route::get('/users', [UserController::class, 'directory'])->middleware('admin');

Route::post('/adduser', [UserController::class, 'store'])->middleware('admin');

Route::get('/user', [UserController::class, 'show'])->middleware('auth');

Route::get('/user/{id}', [UserController::class, 'showAdmin'])->middleware('admin');

Route::put('/edituser/{id}', [UserController::class, 'update'])->middleware('auth');

Route::delete('/deleteuser/{id}', [UserController::class, 'destroy'])->middleware('admin');


// Warehouses

Route::get('/warehouses', [WarehouseController::class, 'list'])->middleware('auth');

Route::get('/warehouses/edit', [WarehouseController::class, 'edit'])->name('newWarehouse')->middleware('auth');

Route::post('/warehouse/store', [WarehouseController::class, 'store'])->middleware('auth');

Route::put('/warehouse/update/{id}', [WarehouseController::class, 'update'])->middleware('auth');

Route::delete('/warehouse/delete/{id}', [WarehouseController::class, 'destroy'])->middleware('admin');

Route::put('/transferingUnits/{id}', [WarehouseController::class, 'transferingUnits'])->middleware('employee');

Route::put('/transferingBoxes/{id}', [WarehouseController::class, 'transferingBoxes'])->middleware('employee');


// Stock

Route::get('/stock', [StockController::class, 'allStock'])->name('stockList')->middleware('auth');

Route::get('/stock/{warehouseId}', [StockController::class, 'warehouseStock'])->middleware('auth');

Route::post('prepare/{id}', [StockController::class, 'prepareOrder'])->middleware('auth');

Route::post('prepare/{id}/changeStatus', [StockController::class, 'prepareOrder'])->middleware('auth');

Route::get('/product/{id}/stock', [StockController::class, 'show'])->middleware('auth');

Route::get('/products/syncWoocommerce', [StockController::class, 'syncWoocommerce'])->name('syncWoocommerce')->middleware('auth');

Route::put('/updatingBoxes/{id}', [StockController::class, 'updatingBoxes'])->middleware('admin');

Route::put('/updatingUnits/{id}', [StockController::class, 'updatingUnits'])->middleware('admin');

Route::delete('/removeProduct/{id}', [OrderController::class, 'removeProduct'])->middleware('auth');

Route::get('/orderPreview/{id}', [OrderController::class, 'orderPreview'])->name('orderPreview')->middleware('auth');

Route::post('/orderToPending/{id}', [OrderController::class, 'orderToPending'])->middleware('auth');
// Route::get('/orderToPending/{id}', [OrderController::class, 'orderToPendingGet'])->middleware('auth'); // Ruta de prueba


// Concepts

Route::get('/concepts', [ConceptController::class, 'show'])->name('concepts')->middleware('auth');

Route::post('/createConcept', [ConceptController::class, 'create'])->name('createConcept')->middleware('auth');

Route::put('/updateConcept', [ConceptController::class, 'update'])->name('updateConcept')->middleware('auth');

Route::delete('/deleteConcept', [ConceptController::class, 'delete'])->name('deleteConcept')->middleware('auth');


// History

Route::get('/sales', [OrderController::class, 'historySales'])->name('historySales')->middleware('auth');

// Import/export products

Route::get('/products/loadcsv', [StockController::class, 'loadcsv'])->middleware(('auth'));

Route::post('/products/loadcsv', [StockController::class, 'storecsv'])->middleware(('auth'))->name('csv-import');
