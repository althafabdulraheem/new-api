<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,NewsController,UserPreferenceController};

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

//auth routes

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login'])->name('login');

// auth routes ends
Route::group(['middleware'=>'auth:sanctum'],function()
{
    Route::get('/news',[NewsController::class,'index']);
    Route::get('/news-filter',[NewsController::class,'filter']);
    Route::get('/news/{slug}',[NewsController::class,'news']); //for retreving single news
    Route::get('/user-preferences',[UserPreferenceController::class,'getPreferences']);
    Route::post('/user-preferences',[UserPreferenceController::class,'setPreferences']);

    Route::post('/logout',[AuthController::class,'logout']);;

});