<?php

namespace App\Http\Controllers\API;

use App\ApiDashboard;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getJumlahDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJumlahDosen();
        
        return response()->json($result);
    }

    public function getJumlahTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getJumlahTendik();

        return response()->json($result);
    }

    public function getJabfungDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJabfungDosen();
       
        return response()->json($result);
    }

    public function getJaPenDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJaPenDosen();
       
        return response()->json($result);
    }

    public function getJaPenTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getJaPenTendik();
       
        return response()->json($result);
    }

    public function getJumSerDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJumSerDosen();
       
        return response()->json($result);
    }
    public function getJumSerLulusTdkLulus()
    {
        $model = new ApiDashboard();
        $result = $model->getJumSerLulusTdkLulus();
       
        return response()->json($result);
    }

    public function getUsiaJekelDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getUsiaJekelDosen();
       
        return response()->json($result);
    }
    public function getIkatanKerjaDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getIkatanKerjaDosen();
       
        return response()->json($result);
    }
    public function getJumGolonganDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJumGolonganDosen();
       
        return response()->json($result);
    }
    public function getBentukPendDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getBentukPendDosen();
       
        return response()->json($result);
    }
    public function getBentukPendTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getBentukPendTendik();
       
        return response()->json($result);
    }
    public function getJumPangkatDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJumPangkatDosen();
       
        return response()->json($result);
    }
    public function getJenjPendDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJenjPendDosen();
       
        return response()->json($result);
    }
    public function getJumPangkatTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getJumPangkatTendik();
       
        return response()->json($result);
    }
    public function getJumPangDosenperAhli()
    {
        $model = new ApiDashboard();
        $result = $model->getJumPangDosenperAhli();
       
        return response()->json($result);
    }
    public function getJumPTAktifPerProv()
    {
        $model = new ApiDashboard();
        $result = $model->getJumPTAktifPerProv();
       
        return response()->json($result);
    }
    public function getJumPTAktifperBentPend()
    {
        $model = new ApiDashboard();
        $result = $model->getJumPTAktifperBentPend();
       
        return response()->json($result);
    }

    //Trend
    public function getTrendJumDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getTrendJumDosen();
       
        return response()->json($result);
    }
    public function getTrendJumTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getTrendJumTendik();
       
        return response()->json($result);
    }
    public function getTrendBentukPend()
    {
        $model = new ApiDashboard();
        $result = $model->getTrendBentukPend();
       
        return response()->json($result);
    }
    public function getTrendSertDosen() //NIDN-NIDK
    {
        $model = new ApiDashboard();
        $result = $model->getTrendSertDosen();
       
        return response()->json($result);
    }
    public function getTrendSertDosenLulusTdkLulus() //2020-2023(Sekarang)
    {
        $model = new ApiDashboard();
        $result = $model->getTrendSertDosenLulusTdkLulus();
       
        return response()->json($result);
    }
    public function getTrendUsulanSerdos() //2020-2023
    {
        $model = new ApiDashboard();
        $result = $model->getTrendUsulanSerdos();
       
        return response()->json($result);
    }
}
