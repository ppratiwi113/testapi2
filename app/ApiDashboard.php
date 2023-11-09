<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiDashboard extends Model
{
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

    public function getJumlahTendik()
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
        AND tsdm.id_jns_sdm = 13
        AND tsp.stat_sp = 'A'
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
        AND treg.id_jns_keluar IS NULL
        GROUP BY tkeaktifan.id_thn_ajaran, tsdm.id_stat_aktif

        ");

        return $data;
    }

    public function getJabfungDosen()
    {
        $data = DB::select
        ("
                SELECT
        CASE
            WHEN rjabfung.nm_jabfung IN ('Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor') THEN rjabfung.nm_jabfung
            ELSE 'Lainnya'
        END AS jabfung,
        COUNT(DISTINCT ps.id_sdm) AS total
        FROM pdrd.sdm ps
        JOIN pdrd.rwy_fungsional prf ON ps.id_sdm = prf.id_sdm AND prf.soft_delete = 0
        JOIN pdrd.reg_ptk treg ON treg.id_sdm = ps.id_sdm AND treg.soft_delete = 0
        JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        JOIN ref.jabfung rjabfung ON prf.id_jabfung = rjabfung.id_jabfung
        JOIN ref.jenis_sdm rjenis ON ps.id_jns_sdm = rjenis.id_jns_sdm
        LEFT JOIN (
        SELECT id_sdm, MAX(id_jabfung) AS max_jabfung
        FROM pdrd.rwy_fungsional
        WHERE soft_delete = 0
        GROUP BY id_sdm
        ) AS MaxJabfung ON ps.id_sdm = MaxJabfung.id_sdm
        WHERE rjenis.nm_jns_sdm = 'Dosen'
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND ps.soft_delete = 0
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND ps.id_stat_aktif IN ('1', '20', '24', '25', '27')
        AND treg.id_jns_keluar IS NULL
        AND tkeaktifan.a_sp_homebase = 1
        GROUP BY
        CASE
            WHEN rjabfung.nm_jabfung IN ('Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor') THEN rjabfung.nm_jabfung
            ELSE 'Lainnya'
        END;

        ");

        return $data;
    }

    public function getJaPenDosen()
    {
        $data = DB::select
        ("
                SELECT
            CASE
                WHEN rjd.nm_jenj_didik IN ('D1', 'D2', 'D3', 'D4', 'Informal', 'Lainnya', 'Non formal',
                'Profesi', 'S1', 'S2', 'S2 Terapan', 'S3', 'S3 Terapan', 'SMA / sederajat', 'Sp-1', 'Sp-2') THEN rjd.nm_jenj_didik
                ELSE 'Tanpa Jenjang'
            END AS jenjang_pendidikan,
            COUNT(DISTINCT prp.id_sdm) AS jml_dosen,
            MAX(ps.last_update) AS last_update
        FROM
            pdrd.reg_ptk AS prp
        JOIN
            pdrd.keaktifan_ptk AS pkp ON prp.id_reg_ptk = pkp.id_reg_ptk
        JOIN
            pdrd.sdm AS ps ON prp.id_sdm = ps.id_sdm
        JOIN (
            SELECT id_sdm, MAX(id_jenj_didik) AS max_id_jenj_didik
            FROM pdrd.rwy_pend_formal
            GROUP BY id_sdm
        ) AS max_jenjang ON prp.id_sdm = max_jenjang.id_sdm
        JOIN
            pdrd.rwy_pend_formal AS prpf ON prp.id_sdm = prpf.id_sdm AND prpf.id_jenj_didik = max_jenjang.max_id_jenj_didik
        JOIN
            ref.jenjang_pendidikan AS rjd ON prpf.id_jenj_didik = rjd.id_jenj_didik
        WHERE
            ps.id_jns_sdm = 12
        AND
            ps.id_stat_aktif = 1
        AND
            ps.soft_delete = 0
        AND
            pkp.soft_delete = 0
        AND
            prp.soft_delete = 0
        GROUP BY
            rjd.nm_jenj_didik
        ORDER BY
            rjd.nm_jenj_didik;

        ");

        return $data;
    }

    // public function getJaPenDosen()
    // {
    //     $data = DB::select
    //     ("
    //             SELECT
    //         CASE
    //             WHEN rjd.nm_jenj_didik IN ('D1', 'D2', 'D3', 'D4', 'Informal', 'Lainnya', 'Non formal',
    //             'Profesi', 'S1', 'S2', 'S2 Terapan', 'S3', 'S3 Terapan', 'SMA / sederajat', 'Sp-1', 'Sp-2') THEN rjd.nm_jenj_didik
    //             ELSE 'Tanpa Jenjang'
    //         END AS jenjang_pendidikan,
    //         COUNT(DISTINCT prp.id_sdm) AS jml_dosen,
    //         MAX(ps.last_update) AS last_update
    //     FROM
    //         pdrd.reg_ptk AS prp
    //     JOIN
    //         pdrd.keaktifan_ptk AS pkp ON prp.id_reg_ptk = pkp.id_reg_ptk
    //     JOIN
    //         pdrd.sdm AS ps ON prp.id_sdm = ps.id_sdm
    //     JOIN (
    //         SELECT id_sdm, MAX(id_jenj_didik) AS max_id_jenj_didik
    //         FROM pdrd.rwy_pend_formal
    //         GROUP BY id_sdm
    //     ) AS max_jenjang ON prp.id_sdm = max_jenjang.id_sdm
    //     JOIN
    //         pdrd.rwy_pend_formal AS prpf ON prp.id_sdm = prpf.id_sdm AND prpf.id_jenj_didik = max_jenjang.max_id_jenj_didik
    //     JOIN
    //         ref.jenjang_pendidikan AS rjd ON prpf.id_jenj_didik = rjd.id_jenj_didik
    //     WHERE
    //         ps.id_jns_sdm = 12
    //     AND
    //         ps.id_stat_aktif = 1
    //     AND
    //         ps.soft_delete = 0
    //     AND
    //         pkp.soft_delete = 0
    //     AND
    //         prp.soft_delete = 0
    //     GROUP BY
    //         rjd.nm_jenj_didik
    //     ORDER BY
    //         rjd.nm_jenj_didik;

    //     ");

    //     return $data;
    // }


    
}