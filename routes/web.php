<?php

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
    $title1 = '首页';
    $title2 = null;
    $titleLink1 = '/';
    $titleLink2 = null;
    return view('kpiIndex', compact('title1', 'title2', 'titleLink1', 'titleLink2'));
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/send_message', 'Auth\RegisterController@sendMessage');
