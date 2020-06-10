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
|ph
*/

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('addgame','GameController@addGame');
Route::get('getgame','GameController@getAllGame');
Route::group(['middleware' => ['auth:api']], function(){
    Route::get('email/resend', 'API\VerificationController@resend')->name('verification.resend'); //name is used for named routes
    Route::get('email/verify/{id}/{hash}', 'API\VerificationController@verify')->name('verification.verify');
    Route::get('details', 'API\UserController@details');
    Route::post('addeditreview','ReviewController@addEditReview');
    Route::delete('deletereviewadmin/{game_id}','ReviewController@deleteReviewAdmin');
    Route::delete('deletereviewuser/{game_id}','ReviewController@deleteReviewUser');


});
Route::group(['middleware' => ['auth:api','verified']], function(){
    Route::post('addpicture', 'API\UserController@setProfilePicture');
    Route::get('getimage', 'API\UserController@getimage');

});
Route::group(['middleware' => ['admin','auth:api']], function(){
    Route::get('admin', 'API\UserController@test');});
