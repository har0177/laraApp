<?php

use Illuminate\Http\Request;

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


use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(['user' => 'API\UserController']);
Route::apiResources(['customer' => 'API\CustomerController']);
Route::apiResources(['vendor' => 'API\VendorController']);
Route::apiResources(['product' => 'API\ProductController']);
Route::apiResources(['category' => 'API\CategoryController']);
Route::get('profile', 'API\UserController@profile');
Route::get('findUser', 'API\UserController@search');
Route::get('findProduct', 'API\ProductController@search');
Route::get('findCustomer', 'API\CustomerController@search');
Route::get('findCategory', 'API\CategoryController@search');
Route::get('findVendor', 'API\VendorController@search');
Route::put('profile', 'API\UserController@updateProfile');