<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EmployeeController;

Route::options('/products', function () {
    return response('', 204);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/current-time', function () {
    return response()->json([
        'success' => true,
        'time' => Carbon::now()->toDateTimeString(),
        'timestamp' => time(),
        'timezone' => config('app.timezone'),
    ]);
});

Route::middleware(['apiKey'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete']);
    Route::post('/products/import', [ProductController::class, 'importCsv']);
    Route::post('/products/bulk-checkout', [ProductController::class, 'bulkCheckout']);
});