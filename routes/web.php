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

use App\Events\TaskCreated;
use App\Project;
use App\Task;

//class Order {
//    public $id;
//
//    public function __construct($id)
//    {
//        $this->id = $id;
//    }
//}


Route::get('/', function () {
    return view('welcome');
});


Route::get('/tasks', function(){
    return Task::latest()->pluck('body');
});

Route::post('/tasks', function(){
    $task = Task::forceCreate(['body' => request('body')]);
    event(
        (new TaskCreated($task))
    );
});

Route::get('/projects/{project}', function(Project $project){
    $project->load('tasks');
    return view('projects.show')->with('project', $project);
});

Route::post('/api/projects/{project}', function(Project $project){
    $task = $project->tasks()->create(['body'=> request('body')]);
    event(new TaskCreated($task));
});

Route::get('/api/projects/{project}', function(Project $project){
    return $project->tasks->pluck('body');
});


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::view('scan', 'scan');

Route::get('/threads', 'ThreadsController@index')->name('threads');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/search', 'SearchController@show');

Route::get('/threads/{channel}', 'ThreadsController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update');
Route::post('locked-thread/{thread}', 'LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');
Route::delete('locked-thread/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.destroy')->middleware('admin');
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
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');
Route::patch('/replies/{reply}', 'RepliesController@update');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');


//Route::middleware('throttle:1')->post('/threads/{channel}/{thread}/replies', 'RepliesController@store'); // We have replaced this throttle protection with policy protection
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');

Route::get('api/users', 'Api\UsersController@index');
Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');

Route::get('register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');

//Route::resource('threads/{channel}/','ThreadsController');


