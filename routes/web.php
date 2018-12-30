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
    return view('welcome');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/{channel}', 'ThreadsController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
Route::post('/threads/{channel}/{thread}/subscription', 'ThreadsSubscriptionsController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscription', 'ThreadsSubscriptionsController@destroy')->middleware('auth');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy');
Route::get('/profile/{user}', "ProfilesController@show")->name('profile');
Route::delete('/profile/{user}/notifications/{notification}', "UserNotificationsController@destroy");
Route::get('/profile/{user}/notifications/', "UserNotificationsController@index");

Route::post('/threads/', 'ThreadsController@store')->middleware('must-be-confirmed');
Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');
Route::delete('/replies/{reply}', 'RepliesController@destroy');
Route::patch('/replies/{reply}', 'RepliesController@update');

//Route::middleware('throttle:1')->post('/threads/{channel}/{thread}/replies', 'RepliesController@store'); // We have replaced this throttle protection with policy protection
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');

Route::get('api/users', 'Api\UsersController@index');
Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');



//Route::resource('threads/{channel}/','ThreadsController');


