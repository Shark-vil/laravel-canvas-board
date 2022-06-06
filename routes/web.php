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
	return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
	Route::get('/dashboard/canvas', function () {
		return view('canvas');
	})->name('canvas');

	// API: Image
	Route::prefix('api')->post(
		'/board/image/add',
		'App\Http\Controllers\Api\Board\ImageController@add'
	);

	Route::prefix('api')->post(
		'/board/image/delete/{id}',
		'App\Http\Controllers\Api\Board\ImageController@delete'
	);

	Route::prefix('api')->post(
		'/board/image/update/{id}',
		'App\Http\Controllers\Api\Board\ImageController@update'
	);
	
	Route::prefix('api')->get(
		'/board/image/get/{id?}',
		'App\Http\Controllers\Api\Board\ImageController@get'
	);

	// API: Text
	Route::prefix('api')->post(
		'/board/text/add',
		'App\Http\Controllers\Api\Board\TextController@add'
	);

	Route::prefix('api')->post(
		'/board/text/delete/{id}',
		'App\Http\Controllers\Api\Board\TextController@delete'
	);

	Route::prefix('api')->post(
		'/board/text/update/{id}',
		'App\Http\Controllers\Api\Board\TextController@update'
	);
	
	Route::prefix('api')->get(
		'/board/text/get/{id?}',
		'App\Http\Controllers\Api\Board\TextController@get'
	);
});

require __DIR__ . '/auth.php';
