<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ApiDashboard;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    public function getJumlahDosen()
    {
        $model = new ApiDashboard();
        $result = $model->getJumlahDosen();
        // dd($result)


        return view('jumlah-dosen', ['jumlahDosen' => $result]);
    }
    public function getJumlahTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getJumlahTendik();
        // dd($result)


        return view('jumlah-tendik', ['jumlahTendik' => $result]);
    }
}
