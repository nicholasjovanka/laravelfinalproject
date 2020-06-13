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
Route::post('getgamename','GameController@filterGameName');
Route::get('getallgamename', 'GameController@getAllGame');
Route::get('getcertaingame/{id}', 'GameController@getCertainGame');
Route::get('getgameimage/{id}', 'GameController@getGameImage');
Route::get('getlatestgame', 'GameController@getLatestGame');
Route::get('getsteamgame/{id}', 'GameController@getSteamGame');
Route::get('getfiveReview/{id}','ReviewController@getfiveReview');
Route::get('getallreview/{id}','ReviewController@getAllReview');
Route::get('getusername/{id}', 'API\UserController@username');
Route::get('calculateScore/{id}', 'ReviewController@CalculateScore');
Route::get('email/verify/{id}/{hash}', 'API\VerificationController@verify')->name('verification.verify');
Route::group(['middleware' => ['auth:api','verified']], function(){
    Route::get('isAdmin', 'API\UserController@isAdmin');
    Route::post('updateuserprofile', 'API\UserController@updateProfile');
    Route::post('addpicture', 'API\UserController@setProfilePicture');
    Route::get('isLoggedIn','API\UserController@isLoggedIn');
    Route::post('verify', 'API\VerificationController@verify');
    Route::post('addeditreview','ReviewController@addEditReview');
    Route::delete('deletereviewuser/{game_id}','ReviewController@deleteReviewUser');
    Route::get('getspecificuserreview/{game_id}','ReviewController@getSpecificUserReview');
});

Route::group(['middleware' => ['auth:api','admin']], function(){
    Route::post('addgame', 'GameController@createGame');
    Route::post('updategame/{id}', 'GameController@updateGame');
    Route::post('setgameimage/{id}', 'GameController@setGamePicture');
    Route::delete('deletegame/{id}', 'GameController@deleteGame');
    Route::delete('deletereviewadmin/{user_id}/{game_id}','ReviewController@deleteReviewAdmin');
});

Route::group(['middleware' => ['auth:api']], function(){
    Route::get('checkverification', 'API\UserController@verifyemail');
    Route::get('email/resend', 'API\VerificationController@resend')->name('verification.resend'); //name is used for named routes
    Route::get('getimage', 'API\UserController@getUserImage');
    Route::get('getdetail', 'API\UserController@getUserDetails');
});
