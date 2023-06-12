<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaypixController;
use App\Http\Controllers\ItauController;
use App\Http\Controllers\ItauExtractsController;
use App\Http\Controllers\SocialmoneyController;
use App\Http\Controllers\BrandEvaluatorController;
use App\Models\Itau_Balance;

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

Route::resource('login', AuthController::class);

Route::middleware('auth:api')->group(function () {
    Route::resource('users', UsersController::class);
    Route::resource('playpix', PlaypixController::class);
    Route::resource('socialmoney', SocialmoneyController::class);
    Route::resource('avaliador', BrandEvaluatorController::class);
    Route::resource('itau', ItauController::class);
    Route::resource('itau.extracts', ItauExtractsController::class);
});