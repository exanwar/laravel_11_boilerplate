<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\auth\RegisterController;
use App\Http\Controllers\API\auth\LoginController;
use App\Http\Controllers\API\auth\RefreshApiController;
use App\Http\Controllers\API\auth\LogoutController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout']);
Route::post('refresh', [RefreshApiController::class, 'refreshToken']);

//Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
////    Route::post('checkout', 'CheckoutController@checkout')->name('checkout');
//    Route::group(['namespace' => 'Common', 'prefix' => 'common', 'middleware' => ['auth:api']], function () {
//        Route::get('info', 'UserController@info');
//        Route::get('abilities', 'AbilityController@index');
//    });
//});

Route::group(['namespace' => 'App\Http\Controllers\API\acl', 'middleware' => ['auth:api'], 'prefix' => 'acl'], function() {
    //Dashboard
    Route::get('ability', 'AbilityController@index');
    Route::resource('permissions', 'PermissionsController')->except(['create', 'edit']);
    Route::get('find-permission', 'PermissionsController@findPermission')->name('find-permission');
    Route::resource('roles', 'RolesController')->except(['create', 'edit']);
    Route::get('find-role', 'RolesController@findRole')->name('find-permission');
});

Route::group(['namespace' => 'App\Http\Controllers\API\controller', 'middleware' => [], 'prefix' => 'user'], function() {
    Route::resource('users', 'UsersController')->except(['create', 'edit']);
    Route::get('find-user', 'UsersController@findUser')->name('find-user');
});