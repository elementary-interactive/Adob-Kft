<?php

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;


use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
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

Route::get('/termek/{slug}', [\App\Http\Controllers\ProductController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9-_]+$')
    ->name('product.show');

Route::get('/termekek/{slug?}', [\App\Http\Controllers\CategoryController::class, 'browse'])
    ->where('slug', '[a-z0-9-_\/]+$')
    ->name('product.browse');

Route::get('/markak', [\App\Http\Controllers\BrandController::class, 'brands'])
    ->name('brands.index');

Route::get('/markak/{brand}/{slug?}', [\App\Http\Controllers\BrandController::class, 'browse'])
    ->where('brand', '[a-z0-9-_]+$')
    ->where('slug', '[a-z0-9-_\/]+$')
    ->name('brands.browse');

Route::get('/', [\App\Http\Controllers\CategoryController::class, 'browse'])
    ->name('index');

Route::get('/kereses', [\App\Http\Controllers\SearchController::class, 'search'])
    ->name('search');

Route::get('download', [\App\Http\Controllers\DownloadController::class, 'download'])
    ->name('export.download') //-;
    ->middleware('signed');

Route::post('/kepek', [\App\Http\Controllers\ProductController::class, 'addImage'])
    ->name('product.images.upload') //-;
    ->middleware('signed');

Route::get('/kepek/feltoltes', function () {
    return response()->json([
        "url"   => URL::temporarySignedRoute('product.images.upload', now()->addMinutes(10))
    ], 200);
    // echo URL::temporarySignedRoute('product.images.upload', now()->addMinutes(10));
});

Route::get('tempcreate', function () {
    return response()->json([
        "url"   => URL::temporarySignedRoute('tempcreate.download', now()->addMinutes(5))
    ], 200);
});

Route::get('temread', function (Request $request) {
    if (!$request->hasValidSignature()) {
        echo "nem valid";
    } else {
        echo "valid";
    }
})
    ->name('tempcreate.download') //-;
    ->middleware('signed');


Route::get('kriksz-kraksz', function () {
    dd(app('site')->current());
});

Route::get('tempexport', function () {
    try {
        $new = \App\Models\ProductExport::create([
            // 'data'  => $record['data'],
            'file'           => 'temp' . date('YmdHis'),
            'satus'          => 'waiting'
        ]);
        (new \App\Exports\ADOBProductsExport_new($new))->store($new->file);
    } catch (\Exception $e) {
        // $this->logger->info('Export error', (array) $e);
        dump($e);
    } catch (\Throwable $e) {
        // $this->logger->info('Export error', (array) $e);
        dump($e);
    }
});

// Route::get('lofasz/{id}', function($id) {
//     $batch  = Bus::findBatch($id);
//     // dump($batch->failedJobIds);
//     // DB::enableQueryLog();
//     $jobs   = DB::table('failed_jobs')->whereIn('uuid', $batch->failedJobIds)->get();

//     // dd($jobs, DB::getQueryLog());
//     $result = [];
//     foreach ($jobs as $job) {
//         $result[] = $job->exception;
//         // });
//     }

//     dd($result);
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
