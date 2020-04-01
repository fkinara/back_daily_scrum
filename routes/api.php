<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');

Route::middleware(['jwt.verify'])->group(function(){
    Route::delete('user/{id}', "UserController@delete"); 
    Route::get('user/{id}', 'UserController@index'); 
    Route::get('user/{limit}/{offset}', "UserController@getAll"); 
	Route::post('logout', "UserController@logout"); 
    
    Route::get('dailyscrum/{id}', "DailyScrumController@index"); 
    Route::get('dailyscrum/{limit}/{offset}', "DailyScrumController@getAll"); 
	Route::post('dailyscrum', 'DailyScrumController@store'); 
	Route::delete('dailyscrum/{id}', "DailyScrumController@delete"); 
});
