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

Route::post('login', 'LoginController@login');
Route::post('me', 'LoginController@me');
Route::post('get/date', 'ConversationController@getCnDate');

Route::group(['prefix' => 'home'], function ($router) {
    Route::post('room/detail', 'HomeController@roomDetail');
    Route::post('room/join', 'HomeController@joinRoom');
    Route::post('room/history/message', 'HomeController@historyMessage');
    Route::post('send/message', 'HomeController@send');
    Route::post('room/list', 'HomeController@roomList');
    Route::post('my/room/list', 'HomeController@myRoomList');
    Route::post('create/party', 'HomeController@createParty');
    Route::post('random/join', 'HomeController@randomJoin');
    Route::post('party/collection', 'HomeController@collection');
    Route::post('party/collection/list', 'HomeController@collectionList');
    Route::post('party/collection/del', 'HomeController@collectionDel');
    Route::post('friend/apply', 'HomeController@friendApply');
    Route::post('message/number', 'HomeController@messageNumber');
    Route::post('friend/apply/list', 'HomeController@friendApplyList');
    Route::post('friend/apply/do', 'HomeController@friendApplyDo');
    Route::post('avatar/list', 'HomeController@avatarList');
    Route::post('change/user/info', 'HomeController@changeUserInfo');
    Route::post('change/user/pass', 'HomeController@changeUserPass');
});

Route::group(['prefix' => 'auction'], function ($router) {
    Route::post('create', 'AuctionController@create');
    Route::post('cron/reload', 'AuctionController@reload');
});





