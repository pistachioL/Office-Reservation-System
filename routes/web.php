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

Route::get('user/{id}',function($id){
   return "用户ID".$id;
});

Route::any('test','orsController@test');

Route::get('basic',function(){
	return 'Hello Wordld';
});
