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

Route::post('push','SwController@push');

/**
 * 原IM系统GetController
 */
//会话列表
Route::post('im/get/chatList','GetController@getChatList');
//获取基本信息
Route::post('im/get/base','GetController@getBase');
//获取会话数据
Route::post('im/get/chatData','GetController@getChatData');

/**
 * 原IM系统GoodsController
 */
//创建商品消息会话
Route::post('im/goods/createGoodsChat','GoodsController@createGoodsChat');
//普通商品消息推送
Route::post('im/goods/pushGoodsMessage','GoodsController@pushGoodsMessage');
//商品信息修改消息推送
Route::post('im/goods/updateGoods','GoodsController@updateGoods');


//Conversation 会话管理
Route::post('im/conversation/create','ConversationController@create');
Route::post('im/conversation/send','ConversationController@send');

