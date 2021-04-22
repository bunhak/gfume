<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubSubCategoryController;

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

    Route::group(['prefix'=>'admin/brand'],function () {
        Route::post('createBrand',[BrandController::class,'createBrand']);
    });

    Route::group(['prefix'=>'admin/subCategory'],function () {
        Route::post('createSubCategory',[SubCategoryController::class,'createSubCategory']);
    });

    Route::group(['prefix'=>'admin/subSubCategory'],function () {
        Route::post('createSubSubCategory',[SubSubCategoryController::class,'createSubSubCategory']);
    });



    Route::group(['prefix'=>'admin/shop'],function () {
        Route::post('createShop',[ShopController::class,'createShop']);
    });

    Route::group(['prefix'=>'admin/color'],function () {
        Route::post('createColor',[ColorController::class,'createColor']);
    });

    Route::group(['prefix'=>'admin/size'],function () {
        Route::post('createSize',[SizeController::class,'createSize']);
    });


    Route::group(['prefix'=>'admin/item'],function () {
        Route::post('createNewItem',[ItemController::class,'createNewItem']);
        Route::post('getAllItem',[ItemController::class,'getAllItem']);
        Route::get('getItemProperty',[ItemController::class,'getItemProperty']);
    });

    Route::group(['prefix'=>'admin/itemDetail'],function () {
        Route::post('createItemDetail',[ItemDetailController::class,'createItemDetail']);
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
