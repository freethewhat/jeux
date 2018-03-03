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

// Routes that will give you items dealing with the home page
Route::get('/', 'HomeController@index')->name('landingPage');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('test', 'GameController@saveGameApiData');

//Routes that will give you items dealing with games
Route::get('/browse', 'GameController@index')->name('browse');
Route::get('/browse/{name}', 'GameController@show');//->name();

Route::resource('games','GameController');

//Routes that will give you items dealing with the user
Route::get('/user', 'UserController@index');//->name();
Route::get('/users/{username}', 'UserController@show');//->name();

Route::resource('users','UserController');

// Authentication
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/settings', 'UserController@showSettings')->name('settings');

Route::get('/searchresults', 'HomeController@showSearchResults')->name('searchresults');
