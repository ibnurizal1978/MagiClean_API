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

/*Route::get('/', function () { return view('welcome'); }); */
Route::get('/', function () { return view('login'); });
Route::post('/login', 'LoginController@login');
Route::get('/home', ['as' => 'home', 'uses' => 'LoginController@home']);
Route::get('/logout', 'LoginController@logout')->name('logout');
Route::get('/emailVoucher', function() { return view('/email/emailVoucher'); });
//cron
Route::get('/deleteSession', 'cron\CronController@deleteSession');
Route::get('/weeklyReport', 'cron\CronController@weeklyReport');

Route::group(['middleware' => 'checkSession'], function () {

    Route::get('/leaderboard/view', 'LeaderboardController@view')->name('leaderboard/view');

    //Users
    Route::get('/users/view', 'UsersController@view')->name('users/view');
    Route::get('/users/usersNew', 'UsersController@new');
    Route::get('/users/usersDetail/{id}', 'UsersController@detail');
    Route::post('/users/add', 'UsersController@add');
    Route::post('/users/edit', 'UsersController@edit')->name('users/edit');
    Route::get('/users/search', 'UsersController@search');

    //report
    Route::get('/report/report1', 'ReportController@report1')->name('report/report1');
    Route::get('/report/report2', 'ReportController@report2')->name('report/report2');

    //log
    Route::get('/log/otp', 'LogController@otp')->name('log/otp');
    Route::get('/log/emailVoucher', 'LogController@emailVoucher')->name('log/emailVoucher');
});