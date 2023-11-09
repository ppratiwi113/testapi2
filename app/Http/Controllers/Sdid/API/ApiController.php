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


}