<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Các route API cho ứng dụng.
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login',   [AuthController::class, 'login']);
    Route::post('register',      [AuthController::class, 'register']);
    Route::post('me',      [AuthController::class, 'me']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
