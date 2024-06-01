<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlaceOrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Models\BootMethod;
use App\Models\HomeContent;
use App\Models\PointsPerLevel;
use App\Models\Skill;
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
Route::get('payment-methods', [PaymentController::class, 'index']);

// group of games routes
Route::group(['prefix' => 'games'], function () {
    Route::get('/', [GamesController::class, 'index']);
    Route::get('/{game}', [GamesController::class, 'show']);
    Route::get('/{game}/details', [GamesController::class, 'showDetails']);
    Route::get('/{game}/quests', [GamesController::class, 'quests']);
    Route::get('/{game}/services', [GamesController::class, 'services']);
});

Route::get('calculate-price', [GamesController::class, 'calculateSkillPrice']);

Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{service}', [ServiceController::class, 'show']);

Route::post('validate-coupon-code', [PlaceOrdersController::class, 'validateCouponCode']);

Route::any('binance_callback', [PaymentController::class, 'binanceCallback']);
