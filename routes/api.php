<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\GoogleMapsController;
use App\Http\Controllers\ResponsibleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:api')->group(function () {
    Route::get('/user/me', [UserController::class, 'me']);
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::get('/maps/autocomplete', [GoogleMapsController::class, 'autocomplete']);
    Route::apiResource('driver', DriverController::class)->only('index', 'store', 'show');
    Route::apiResource('responsible', ResponsibleController::class)->only('index', 'store', 'show');
    Route::apiResource('school', SchoolController::class)->only('index', 'store', 'show', 'update');
    Route::apiResource('student', StudentController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('user.driver', DriverController::class)->only('update', 'destroy');
    Route::apiResource('user.responsible', ResponsibleController::class)->only('update', 'destroy');
});
