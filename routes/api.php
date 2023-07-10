<?php

use App\Http\Controllers\BetanoAdminController;
use App\Http\Controllers\BetanoAdminSaquesController;
use App\Http\Controllers\BetanoController;
use App\Http\Controllers\IgMoneyAdminController;
use App\Http\Controllers\IgMoneyAdminUsersController;
use App\Http\Controllers\IgMoneyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaypixController;
use App\Http\Controllers\PlaypixExtractsController;
use App\Http\Controllers\ItauController;
use App\Http\Controllers\ItauExtractsController;
use App\Http\Controllers\NubankController;
use App\Http\Controllers\NubankExtractsController;
use App\Http\Controllers\SocialmoneyController;
use App\Http\Controllers\BrandEvaluatorController;
use App\Http\Controllers\AvaliadorPremiadoController;
use App\Http\Controllers\AvaliadorPremiadoRefController;
use App\Http\Controllers\InstamoneyController;

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
Route::resource('avaliadorpremiado/ref', AvaliadorPremiadoRefController::class);

Route::middleware('auth:api')->group(function () {
    Route::resource('users', UsersController::class);
    Route::resource('playpix', PlaypixController::class);
    Route::resource('playpix.extracts', PlaypixExtractsController::class);
    Route::resource('socialmoney', SocialmoneyController::class);
    Route::resource('avaliador', BrandEvaluatorController::class);
    Route::resource('avaliadorpremiado', AvaliadorPremiadoController::class);
    Route::resource('itau', ItauController::class);
    Route::resource('itau.extracts', ItauExtractsController::class);
    Route::resource('nubank', NubankController::class);
    Route::resource('nubank.extracts', NubankExtractsController::class);
    Route::resource('instamoney', InstamoneyController::class);

    Route::get('betano/profile', [BetanoController::class, 'profile']);
    Route::resource('betano', BetanoController::class);
    Route::resource('betano-admin', BetanoAdminController::class);
    Route::resource('betano-admin-saques', BetanoAdminSaquesController::class);

    Route::get('igmoney/profile', [IgMoneyController::class, 'profile']);
    Route::put('igmoney/saques', [IgMoneyController::class, 'saques']);
    Route::resource('igmoney', IgMoneyController::class);
    Route::resource('igmoney-admin', IgMoneyAdminController::class);
    Route::resource('igmoney-admin-users', IgMoneyAdminUsersController::class);
});
