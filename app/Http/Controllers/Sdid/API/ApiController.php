<?php

namespace App\Http\Controllers\Sdid\API;

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
}