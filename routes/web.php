<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/termek/{slug}',[\App\Http\Controllers\ProductController::class, 'show'])
    ->where('slug', '[a-z0-9-_\/]+$')
    ->name('product.show');

Route::get('/termekek/{slug?}',[\App\Http\Controllers\CategoryController::class, 'browse'])
    ->where('slug', '[a-z0-9-_\/]+$')
    ->name('product.browse');

Route::get('/markak',[\App\Http\Controllers\BrandController::class, 'brands'])
    ->name('brands.index');

Route::get('/markak/{brand}/{slug?}',[\App\Http\Controllers\BrandController::class, 'browse'])
    ->where('brand', '[a-z0-9-_]+$')
    ->where('slug', '[a-z0-9-_\/]+$')
    ->name('brands.browse');

// Route::get('/', function () {
//     return view('web.pages.categories');
// });

// Route::get('/brands', function () {
//     return view('web.pages.brands');
// });

// Route::get('/pricelists', function () {
//     return view('web.pages.pricelists');
// });

// Route::get('/about', function () {
//     return view('web.pages.about');
// });
