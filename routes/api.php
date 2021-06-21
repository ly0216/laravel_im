<?php

use Illuminate\Http\Request;
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

Route::post('push','SwController@push');
Route::post('im/get/chatList','GetController@getChatList');
Route::post('im/get/base','GetController@getBase');
Route::post('im/get/chatData','GetController@getChatData');


Route::post('login','LoginController@login');
Route::post('me','LoginController@me');

