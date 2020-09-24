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
	return redirect('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/a/{ak}/{y?}','WELCOME@param')->name('param');

Route::get('/{ak}','WELCOME@param')->name('param2');


Route::get('/test/','WELCOME@index')->name('test');



Route::post('/test','WELCOME@store')->name('test.post');



Route::prefix('bot-sat')->middleware('auth:web')->group(function () {
	Route::get('/laporan','SAT@laporan');
});


Route::prefix('bot')->group(function () {
	Route::prefix('sipd')->group(function () {

		Route::prefix('rkpd/{tahun}')->middleware('auth:web')->group(function () {
			Route::get('/','SIPD\RKPD\LISTDATA@index')->name('sipd.rkpd');
			Route::get('/hanlde','SIPD\RKPD\LISTDATA@needHandle')->name('sipd.rkpd.handle');

			Route::get('/get/json/{json_id}','SIPD\RKPD\LISTDATA@getjson')->name('sipd.rkpd.json');

			Route::get('/get-list','SIPD\RKPD\LISTDATA@getData')->name('sipd.rkpd.list.update');
			Route::get('/get-data/{kodepemda}/{status}/{transactioncode}','SIPD\RKPD\GETDATA@getData')->name('sipd.rkpd.data.update');

			Route::get('/get-data-masive','SIPD\RKPD\GETDATA@store_masive')->name('sipd.rkpd.data.masive');

			Route::get('/download/{kodepemda?}','SIPD\RKPD\IO@index')->name('sipd.rkpd.data.download');


		});
			
	});

	Route::prefix('sirup')->group(function () {

		Route::prefix('paket-pekerjaan/{tahun}')->middleware('auth:web')->group(function () {
			Route::get('/','SIPD\RKPD\LISTDATA@index')->name('sirup.paket');
			
			Route::get('/get-data/','SIRUP\PAKETPEKERJAAN\GETDATA@getData')->name('sirup.paket.data.update');
		});
			
	});

	Route::prefix('nuwsp')->group(function () {
		Route::prefix('sat/via-api')->group(function () {
			Route::prefix('data/{tahun}')->middleware('auth:web')->group(function () {
				Route::get('/','SIPD\RKPD\LISTDATA@index')->name('nuwsp.sat');
				Route::get('/get-list','SIPD\RKPD\LISTDATA@getData')->name('nuwsp.sat.list.update');
				Route::get('/get-data/{kodepemda}/{status}/{transactioncode}','SIPD\RKPD\GETDATA@getData')->name('nuwsp.sat.data.update');
			});
			
		});

		Route::prefix('sat/via-scrap')->group(function () {
			Route::prefix('data/{tahun}')->middleware('auth:web')->group(function () {
				Route::get('/','SIPD\RKPD\LISTDATA@index')->name('nuwsp.sat');
				Route::get('/get-list','SIPD\RKPD\LISTDATA@getData')->name('nuwsp.sat.list.update');
				Route::get('/get-data/{kodepemda}/{status}/{transactioncode}','SIPD\RKPD\GETDATA@getData')->name('nuwsp.sat.data.update');
			});
			
		});

	});
});






Route::group(['middleware' => 'auth'], function () {

	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});








