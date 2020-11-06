<?php

use Illuminate\Http\Request;


Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

/**
 * Email
 */
Route::post('/reminder', 'ReminderController@reminder')->name('reminder');
Route::put('/reset','ReminderController@reset')->name('reset');

//ログインユーザーを返す
Route::get('/user',fn()=>Auth::user())->name('user');

// トークンリフレッシュ
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();
    return response()->json();
});
/**
 * ScheduledTweetController
 */
Route::post('/saveScheduled','ScheduledTweetController@saveScheduled')->name('saveScheduled');
Route::post('/getScheduledList','ScheduledTweetController@getScheduledList')->name('getScheduledList');
Route::post('/deleteScheduled','ScheduledTweetController@deleteScheduled')->name('deleteScheduled');
Route::post('/photos', 'ScheduledTweetController@index')->name('photo.index');
/**
 * HomeController
 */
Route::get('/getTwitterIdList','HomeController@getTwitterIdList')->name('getTwitterIdList');
Route::post('/getFollowCountToday','HomeController@getFollowCountToday')->name('getFollowCountToday');
Route::post('/getUnfollowCountToday','HomeController@getUnfollowCountToday')->name('getUnfollowCountToday');
Route::post('/deleteAccountList','HomeController@deleteAccountList')->name('deleteAccountList');
Route::post('/followUnfolloNum','HomeController@followUnfolloNum')->name('followUnfolloNum');
Route::post('/parameCheck','HomeController@parameCheck')->name('parameCheck');

//new
Route::post('/getFollowCount','HomeController@getFollowCount')->name('getFollowCount');
Route::post('/getUnfollowCount','HomeController@getUnfollowCount')->name('getUnfollowCount');
Route::get('/followCount/{twitter_screen_name}','HomeController@totalFollowCount')->name('totalFollowCount');
Route::get('/unfollowCount/{twitter_screen_name}','HomeController@totalUnfollowCount')->name('totalUnfollowCount');

/**
 * ActionFlafController
 */
Route::get('/getErrorFlag','ActionFlagController@getErrorFlag')->name('getErrorFlag');
Route::post('/getActionFlag','ActionFlagController@getActionFlag')->name('getActionFlag');
Route::post('/setStartFlag','ActionFlagController@setStartFlag')->name('setStartFlag');
/**
 * TwitterApiController
 */
Route::post('/followCount','TwitterApiController@followCount')->name('followCount');

/**
 * TwitterSettingController
 */
Route::post('/registerUserTargetList','TwitterSettingController@registerUserTargetList')->name('registerUserTargetList');
Route::post('/getTargetList','TwitterSettingController@getTargetList')->name('getTargetList');
Route::post('/deleteUserFromTargetList','TwitterSettingController@deleteUserFromTargetList')->name('deleteUserFromTargetList');
Route::post('/saveWordList','TwitterSettingController@saveWordList')->name('saveWordList');
Route::post('/getSearchWordList','TwitterSettingController@getSearchWordList')->name('getSearchWordList');
Route::post('/deleteSerachWord','TwitterSettingController@deleteSerachWord')->name('deleteSerachWord');
Route::post('/saveLikeWordList','TwitterSettingController@saveLikeWordList')->name('saveLikeWordList');
Route::post('/getLikeWordList','TwitterSettingController@getLikeWordList')->name('getLikeWordList');
Route::post('/deleteLikeWord','TwitterSettingController@deleteLikeWord')->name('deleteLikeWord');
Route::post('/saveUnfollowSetting','TwitterSettingController@saveUnfollowSetting')->name('saveUnfollowSetting');
Route::post('/getUnfollowSetting','TwitterSettingController@getUnfollowSetting')->name('getUnfollowSetting');

/**
 * __invoke
 */
Route::put('/stopAction','stopAction')->name('stopAction');
Route::put('/Restart','Restart')->name('Restart');
