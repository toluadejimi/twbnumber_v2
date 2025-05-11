<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('webhook',  [HomeController::class,'webhook']);

Route::post('webhook_tella',  [HomeController::class,'tellaWebhook']);

Route::any('online-hook', [HomeController::class, 'simhook']);



Route::any('e-fund',  [HomeController::class,'e_fund']);
Route::any('verify',  [HomeController::class,'e_check']);

Route::post('fund',  [ProductController::class,'e_fund']);







