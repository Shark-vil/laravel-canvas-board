<?php

use Illuminate\Support\Facades\Route;

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
    return view('index');
});

Route::post('/api/board/upload',
	'App\Http\Controllers\Api\Board\UploadImageController@upload');

Route::post('/api/board/update/{id}',
	'App\Http\Controllers\Api\Board\UploadImageController@update');

Route::get('/api/board/get/{id?}',
	'App\Http\Controllers\Api\Board\UploadImageController@get');