<?php
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Sdid\API\JumlahDosenController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('api/jumlah_dosen', 'JumlahDosenController@getJumlahDosen');
// Route::get('/', 'CobaController@index');
// Route::get('/jumlah-dosen', 'CobaController@index');
Route::get('/api/jumlah-dosen', 'Sdid\\API\\ApiController@getJumlahDosen');
Route::get('/api/jumlah-tendik', 'Sdid\\API\\ApiController@getJumlahTendik');
Route::get('/api/jabfungdosen', 'Sdid\\API\\ApiController@getJabfungDosen');
Route::get('/api/japendosen', 'Sdid\\API\\ApiController@getJaPenDosen');