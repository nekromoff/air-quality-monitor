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

Route::get('/', ['uses' => 'SensorController@show', 'as' => 'show'])->middleware('cache.headers:public;max_age=3600;etag');

Route::post('receive{extension}', ['uses' => 'SensorController@receive', 'as' => 'receive']);
