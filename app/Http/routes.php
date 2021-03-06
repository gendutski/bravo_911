<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


// Authentication routes...
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Registration routes...
//~ Route::get('register', 'Auth\AuthController@getRegister');
//~ Route::post('register', 'Auth\AuthController@postRegister');


Route::group(['prefix' => '/', 'middleware' => 'auth'], function(){
	Route::get('', 'HomeController@index');
	Route::resource('account_code', 'AccountCodeController');
	Route::controller('human_resource', 'HumanResourceController');
	Route::resource('jurnall_process', 'JurnallProcessController');
	Route::resource('param', 'ParamController');
	Route::resource('user', 'UserController');
});

