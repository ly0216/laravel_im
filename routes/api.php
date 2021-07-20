<?php

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
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login','LoginController@login');
Route::post('me','LoginController@me');
Route::post('get/date','ConversationController@getCnDate');

Route::group(['prefix' => 'home'], function ($router) {
    Route::post('room/join','HomeController@joinRoom');
    Route::post('send/message','HomeController@send');
});





