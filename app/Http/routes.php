<?php

use Illuminate\Support\Facades\Route;


//login token statis
Route::post ('login', 'Auth\\AuthController@login');
Route::group(['middleware' => 'jwt-auth'], function(){
    Route::get('/api/last-update', 'API\\ApiController@getLastUpdate');
    Route::get('/api/jumlah-dosen', 'API\\ApiController@getJumlahDosen');
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
    Route::get('/api/jmlhpangkattendik', 'API\\ApiController@getJumPangkatTendik');
    Route::get('/api/jmlhpngktdosenahli', 'API\\ApiController@getJumPangDosenperAhli');
    Route::get('/api/jumptaktifprov', 'API\\ApiController@getJumPTAktifPerProv');
    Route::get('/api/jumptaktifpendik', 'API\\ApiController@getJumPTAktifperBentPend');
    Route::get('/api/statkepegawaian-dosen', 'API\\ApiController@getStatKepegawaianDosen');
    Route::get('/api/statkepegawaian-tendik', 'API\\ApiController@getStatKepegawaianTendik');
    Route::get('/api/bkdjenis', 'API\\ApiController@getBKDJenis');
    Route::get('/api/ajuan-perubahandata-dosen', 'API\\ApiController@getAjuanPerubahDataDosen');

    //trend
    Route::get('/api/trendjmlhdosen', 'API\\ApiController@getTrendJumDosen');
    Route::get('/api/trendjmlhtendik', 'API\\ApiController@getTrendJumTendik');
    Route::get('/api/trendbentukpend', 'API\\ApiController@getTrendBentukPend');
    Route::get('/api/trendsertdosen', 'API\\ApiController@getTrendSertDosen'); //NIDN-NIDK
    Route::get('/api/trendsertdosenlulus', 'API\\ApiController@getTrendSertDosenLulusTdkLulus');
    Route::get('/api/trendusulanserdos', 'API\\ApiController@getTrendUsulanSerdos');
    Route::get('/api/trendstatkepegawaian', 'API\\ApiController@getTrendStatKepegawaian');

    // Route::get('/api/trendpak', 'API\\ApiController@getTrendPAK');
});