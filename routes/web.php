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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('index');
});

Auth::routes([
        'register' => false
    ]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/woocommerce', [App\Http\Controllers\WooCommerceController::class, 'wc']);

// Gestionar usuarios (registrar, editar, eliminar)
Route::get('/users', [App\Http\Controllers\UserController::class, 'directory']);

Route::post('/adduser', [App\Http\Controllers\UserController::class, 'store']);
Route::get('/warehouse/list', [App\Http\Controllers\WarehouseController::class, 'list'] );

Route::get('/warehouse/create', function () { return view('createWarehouse'); } );
Route::post('/warehouse/create', [App\Http\Controllers\WarehouseController::class, 'create']);


Route::get('/newProducts', [App\Http\Controllers\ProductsController::class, 'newProducts'] );