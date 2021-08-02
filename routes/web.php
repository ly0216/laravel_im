<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('welcome', ['version' => env('APP_VERSION', '0.0.1')]);
});
Route::get('/login', function () {
    return view('login', ['version' => env('APP_VERSION', '0.0.1')]);
});
Route::get('/home/index', function () {
    return view('home.index', ['version' => env('APP_VERSION', '0.0.1')]);
});

Route::get('/home/room/{chat_sn?}', function ($chat_sn = 1) {
    return view('home.room', ['chat_sn' => $chat_sn, env('APP_VERSION', '0.0.1')]);
});

Route::get('/home/party/create', function () {
    return view('party.create', ['version' => env('APP_VERSION', '0.0.1')]);
});

Route::get('/home/my/collection', function () {
    return view('party.collection', ['version' => env('APP_VERSION', '0.0.1')]);
});

Route::get('/home/apply/list', function () {
    return view('home.apply', ['version' => env('APP_VERSION', '0.0.1')]);
});

Route::get('/home/my/info', function () {
    return view('home.my', ['version' => env('APP_VERSION', '0.0.1')]);
});
