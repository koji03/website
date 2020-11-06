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

//パスワードリセット用ルート
Route::get('/password/{reset}',function () {
    return view('index');
})->name('password')->middleware('signed');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/twitter','TwitterApiController@twitter')->name('twitter');
    Route::get('/callback','TwitterApiController@twitterCallback')->name('twitterCallback');
 });

Route::get('/{any?}', fn() => view('index'))->where('any', '.+');
