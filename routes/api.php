<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\GoogleMapsController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ResponsibleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/trip/schedule', [TripController::class, 'schedule'])->middleware('throttle:9999,1');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/me', [UserController::class, 'me']);
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::put('/trip/{trip}/start', [TripController::class, 'start']);
    Route::put('/trip/{trip}/end', [TripController::class, 'end']);
    Route::put('/trip/{trip}/student/{student}/embark', [TripController::class, 'embark']);
    Route::put('/trip/{trip}/student/{student}/disembark', [TripController::class, 'disembark']);
    Route::put('/trip/{trip}/student/{student}/absent', [TripController::class, 'absent']);
    Route::put('/trip/{trip}/student/{student}/present', [TripController::class, 'present']);
    Route::get('/maps/autocomplete', [GoogleMapsController::class, 'autocomplete']);

    Route::apiResource('driver', DriverController::class)->only('index', 'store', 'show');
    Route::apiResource('itinerary', ItineraryController::class);
    Route::apiResource('responsible', ResponsibleController::class)->only('index', 'store', 'show');
    Route::apiResource('role', RoleController::class);
    Route::apiResource('school', SchoolController::class);
    Route::apiResource('student', StudentController::class);
    Route::apiResource('trip', TripController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('user.driver', DriverController::class)->only('update');
    Route::apiResource('user.responsible', ResponsibleController::class)->only('update');
});
