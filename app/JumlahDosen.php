<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JumlahDosen extends Model
{
    protected $table = 'tsdm.'; 

    public function getJumlahDosen()
    {

        $data = DB::select
        ("
        SELECT COUNT(tsdm.id_sdm) AS total, tsdm.id_stat_aktif
        FROM pdrd.sdm tsdm
        LEFT JOIN pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
        LEFT JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        WHERE tkeaktifan.id_thn_ajaran = 2023
        AND tkeaktifan.a_sp_homebase = 1
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 12
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
        AND treg.id_jns_keluar IS NULL
        GROUP BY tkeaktifan.id_thn_ajaran, tsdm.id_stat_aktif
        ");

        return $data;
    }
}