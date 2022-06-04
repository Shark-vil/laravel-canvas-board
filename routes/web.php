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
		'/board/image/upload',
		'App\Http\Controllers\Api\Board\ImageController@upload'
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
});

require __DIR__ . '/auth.php';
