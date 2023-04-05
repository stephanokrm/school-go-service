<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
//    Route::get('/animal/me', [AnimalController::class, 'me']);
//    Route::get('/interest/me', [InterestController::class, 'me']);
//    Route::delete('/animal/{animal}/interest', [InterestController::class, 'destroy']);
//    Route::get('/animal/{animal}/interest', [InterestController::class, 'show']);
//    Route::get('/animal/{animal}/form', [FormController::class, 'animal']);
//
//    Route::apiResource('animal', AnimalController::class)->except('index', 'show');
//    Route::apiResource('animal.answer', AnswerController::class)->only('store');
//    Route::apiResource('animal.user.answer', AnswerController::class)->only('index');
//    Route::apiResource('animal.image', ImageController::class)->only('index', 'store');
//    Route::apiResource('form', FormController::class)->only('index', 'store', 'show');
//    Route::apiResource('form.question', QuestionController::class)->only('index', 'store');
//    Route::apiResource('question', QuestionController::class)->only('destroy');
//    Route::apiResource('animal.interest', InterestController::class)->only('store');
//    Route::apiResource('breed', BreedController::class);
//    Route::apiResource('image', ImageController::class)->only('destroy');
//    Route::apiResource('interest', InterestController::class)->except('store', 'destroy', 'show');
//    Route::apiResource('user', UserController::class)->except('store');
});
