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
        $jumlahdosen = $model->getJumlahDosen();
        $statpegawai = $model->getStatKepegawaianDosen();
        // dd($result)


        return view('jumlah-dosen', ['jumlahDosen' => $jumlahdosen, 'statPegawai' => $statpegawai]);
    }
    public function getJumlahTendik()
    {
        $model = new ApiDashboard();
        $result = $model->getJumlahTendik();
        // dd($result)


        return view('jumlah-tendik', ['jumlahTendik' => $result]);
    }
}
