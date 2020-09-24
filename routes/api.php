<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

Route::prefix('connection-sinkronisasi')->group(function(){
	Route::prefix('sat')->group(function(){
		Route::get('last-record/{tahun}/{pemda?}','SAT@sat');
		Route::get('laporan-per-daerah/{tahun}/{pemda}','SAT@series_laporan');
		Route::get('list-data/{tahun}/{pemda?}','SAT@list_data');


	});

	
});
