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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/signUp', 'API\MCController@signUp');
Route::post('/v1/login', 'API\MCController@login');
Route::post('/v1/getSession', 'API\MCController@getSession');
Route::post('/v1/sendScore', 'API\MCController@sendScore');
Route::post('/v1/inputOtp', 'API\MCController@inputOtp');
Route::post('/v1/resendOtp', 'API\MCController@resendOtp');
Route::post('/v1/getPage', 'API\MCController@getPage');
Route::post('/v1/viewScore', 'API\MCController@viewScore');
Route::post('/v1/viewScore1', 'API\MCController@viewScore1');

