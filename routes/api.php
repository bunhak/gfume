<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ItemController;

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


// no need token
Route::group(['prefix'=>'category'],function () {
    Route::get('getCategoryHome',[CategoryController::class,'getCategoryHome']);
    Route::get('mockData',[CategoryController::class,'mockData']);
});

// need token
Route::group(['middleware' => 'auth:api'],function (){
    Route::post('/uploadFile',[FileController::class,'uploadFile']);

    Route::group(['prefix'=>'admin/category'],function () {
        Route::post('createCategory',[CategoryController::class,'createCategory']);
    });
    Route::group(['prefix'=>'admin/item'],function () {
        Route::post('addNewItem',[ItemController::class,'addNewItem']);
        Route::post('deleteItem',[ItemController::class,'deleteItem']);
        Route::post('getAllItem',[ItemController::class,'getAllItem']);
        Route::post('getItemDetail',[ItemController::class,'getItemDetail']);
        Route::get('getItemProperty',[ItemController::class,'getItemProperty']);
    });
});


Route::group(['prefix'=>'auth'],function (){
    Route::post('login',[AuthController::class,'login']);
    Route::post('register',[AuthController::class,'register']);
    Route::post('registerSeller',[AuthController::class,'registerSeller']);
    Route::post('registerAdmin',[AuthController::class,'registerAdmin']);
    Route::group(['middleware' => 'auth:api'],function (){
        Route::get('logout',[AuthController::class,'logout']);
        Route::get('user',[AuthController::class,'user']);
    });
});
