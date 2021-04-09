<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/uploadFile',[FileController::class,'uploadFile']);

Route::group(['prefix'=>'category'],function () {
    Route::get('getCategoryHome',[CategoryController::class,'getCategoryHome']);
    Route::get('mockData',[CategoryController::class,'mockData']);
});


Route::group(['prefix'=>'auth'],function (){
    Route::post('login',[AuthController::class,'login']);
    Route::post('register',[AuthController::class,'register']);




    Route::group(['middleware' => 'auth:api'],function (){
        Route::get('logout',[AuthController::class,'logout']);
        Route::get('user',[AuthController::class,'user']);
    });
});
