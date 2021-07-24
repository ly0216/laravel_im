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
    return view('welcome');
});
Route::get('/login', function () {
    return view('login');
});
Route::get('/home/index', function () {
    return view('home.index');
});

Route::get('/home/room/{chat_sn?}', function ($chat_sn = 1) {
    return view('home.room', ['chat_sn' => $chat_sn]);
});

Route::get('/home/party/create', function(){
    return view('party.create');
});
