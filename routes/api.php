<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PlaceOrdersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::any('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ProfileController::class, 'profile']);
    Route::post('place-order', [PlaceOrdersController::class, 'placeOrder']);
    Route::get('orders', [OrdersController::class, 'index']);
});

Route::get('home', [HomeController::class, 'home']);
Route::get('games', [GamesController::class, 'index']);
Route::get('games/{game}', [GamesController::class, 'show']);
Route::get('games/{game}/details', [GamesController::class, 'showDetails']);

Route::post('validate-coupon-code', [PlaceOrdersController::class, 'validateCouponCode']);
