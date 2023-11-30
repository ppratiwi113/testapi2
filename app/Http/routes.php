<?php


// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Tymon\JWTAuth\Facades\JWTAuth;
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

// Route::get('/api/jumlah-dosen', 'API\\ApiController@getJumlahDosen');
Route::get('/api/jumlah-tendik', 'API\\ApiController@getJumlahTendik');
Route::get('/api/jabfungdosen', 'API\\ApiController@getJabfungDosen');
Route::get('/api/japendosen', 'API\\ApiController@getJaPenDosen');
Route::get('/api/japentendik', 'API\\ApiController@getJaPenTendik');
Route::get('/api/jumserdosen', 'API\\ApiController@getJumSerDosen');
Route::get('/api/jumserdosenlulus', 'API\\ApiController@getJumSerLulusTdkLulus');
Route::get('/api/usiajekeldosen', 'API\\ApiController@getUsiaJekelDosen');
Route::get('/api/ikatankerjadosen', 'API\\ApiController@getIkatanKerjaDosen');
Route::get('/api/jumgoldosen', 'API\\ApiController@getJumGolonganDosen');
Route::get('/api/bentukpenddosen', 'API\\ApiController@getBentukPendDosen');
Route::get('/api/bentukpendtendik', 'API\\ApiController@getBentukPendTendik');
Route::get('/api/jmlhpangkatdosen', 'API\\ApiController@getJumPangkatDosen');
Route::get('/api/jenjangpenddosen', 'API\\ApiController@getJenjPendDosen');
Route::get('/api/jmlhpangkattendik', 'API\\ApiController@getJumPangkatTendik');
Route::get('/api/jmlhpngktdosenahli', 'API\\ApiController@getJumPangDosenperAhli');
Route::get('/api/jumptaktifprov', 'API\\ApiController@getJumPTAktifPerProv');
Route::get('/api/jumptaktifpendik', 'API\\ApiController@getJumPTAktifperBentPend');
Route::get('/api/trendjmlhdosen', 'API\\ApiController@getTrendJumDosen');
Route::get('/api/trendjmlhtendik', 'API\\ApiController@getTrendJumTendik');
Route::get('/api/trendbentukpend', 'API\\ApiController@getTrendBentukPend');
Route::get('/api/trendsertdosen', 'API\\ApiController@getTrendSertDosen'); //NIDN-NIDK
Route::get('/api/trendsertdosenlulus', 'API\\ApiController@getTrendSertDosenLulusTdkLulus');
Route::get('/api/trendusulanserdos', 'API\\ApiController@getTrendUsulanSerdos');//masih error
Route::get('/api/trendsertdosen', 'API\\ApiController@getTrendSertDosen');

//login token statis
Route::post ('login', 'Auth\\AuthController@login');
Route::group(['middleware' => 'jwt-auth'], function(){
    Route::get('/api/jumlah-dosen', 'API\\ApiController@getJumlahDosen');
});





// Route::middleware(['csrf.api'])->post('/api/register', 'Auth\\AuthController@register');
// Route::middleware(['csrf.api'])->post('/api/authenticate', 'Auth\AuthController@authenticate');