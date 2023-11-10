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
        SELECT tsdm.id_stat_aktif, COUNT(tsdm.id_sdm) AS total, MAX(tsdm.last_update) AS last_update
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
        AND tsdm.id_stat_aktif IN ('1')
        AND treg.id_jns_keluar IS NULL
        GROUP BY tkeaktifan.id_thn_ajaran, tsdm.id_stat_aktif;
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

    public function getJaPenTendik()
    {
        $data = DB::select
        ("
        SELECT
            CASE
                WHEN rjd.nm_jenj_didik IN ('D1', 'D2', 'D3', 'D4', 'Informal', 'Lainnya', 'Non formal', 'Profesi', 'S1',
                'S2', 'S2 Terapan', 'S3', 'S3 Terapan', 'SMA / sederajat', 'Sp-1', 'Sp-2') THEN rjd.nm_jenj_didik
                ELSE 'Tanpa Jenjang'
            END AS jenjang_pendidikan,
            COUNT(DISTINCT prp.id_sdm) AS jml_tendik,
            MAX(ps.last_update) AS last_update
        FROM
            pdrd.reg_ptk AS prp
        INNER JOIN
            pdrd.keaktifan_ptk AS pkp ON prp.id_reg_ptk = pkp.id_reg_ptk
        INNER JOIN
            pdrd.sdm AS ps ON prp.id_sdm = ps.id_sdm
        INNER JOIN
            pdrd.rwy_pend_formal AS prpf ON prp.id_sdm = prpf.id_sdm
        INNER JOIN
            ref.jenjang_pendidikan AS rjd ON prpf.id_jenj_didik = rjd.id_jenj_didik
        WHERE
            ps.id_jns_sdm = 13
            AND ps.id_stat_aktif = 1
            AND ps.soft_delete = 0
            AND pkp.soft_delete = 0
            AND prp.soft_delete = 0
        GROUP BY
            jenjang_pendidikan
        ORDER BY
            jenjang_pendidikan;

        ");

        return $data;
    }

    public function getJumSerDosen()
    {
        $data = DB::select
        ("
        SELECT 
            CASE 
                WHEN LEFT(ps.nidn, 2) BETWEEN '00' AND '87' THEN 'NIDN'
                WHEN LEFT(ps.nidn, 2) BETWEEN '88' AND '99' THEN 'NIDK'
            END AS nidn_group,
            prs.thn_sert,
            COUNT(*) AS jumlah_sert
        FROM 
            pdrd.rwy_sertifikasi AS prs
        JOIN 
            pdrd.sdm AS ps ON prs.id_sdm = ps.id_sdm AND ps.soft_delete = 0
        WHERE 
            prs.id_jns_sert IN (1, 2, 4)
            AND prs.thn_sert = 2023
            AND prs.soft_delete = 0
            AND LEFT(ps.nidn, 2) BETWEEN '00' AND '99'
        GROUP BY 
            nidn_group, prs.thn_sert
        ORDER BY 
            nidn_group DESC;

        ");

        return $data;
    }

    public function getJumSerLulusTdkLulus()
    {
        $data = DB::select
        ("
        SELECT
            CASE
                WHEN sls.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN sls.simpulan_akhir = 'T' THEN 'Tidak Lulus'
            END AS simpulan_akhir,
            COUNT(*) AS jumlah_sert
        FROM pdrd.rwy_sertifikasi AS prs
        JOIN sdid.reg_serdos AS sr ON sr.id_sdm = prs.id_sdm
        JOIN sdid.lulus_serdos AS sls ON sls.id_usul_dys = sr.id_usul_dys
        WHERE prs.thn_sert = 2023
            AND prs.id_jns_sert IN (1, 2, 4)
            AND prs.soft_delete = 0
        GROUP BY simpulan_akhir;

        ");

        return $data;
    }

    public function getUsiaJekelDosen()
    {
        $data = DB::select
        ("
        SELECT
            tsdm.jk AS jenis_kelamin,
            CASE
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) < 30 THEN '< 30 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 30 AND 39 THEN '30-39 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 40 AND 49 THEN '40-49 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 50 AND 59 THEN '50-59 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) >= 60 THEN '> 60 tahun'
            END AS kelompok_usia,
            COUNT(DISTINCT treg.id_sdm) AS jml_dosen
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
        GROUP BY
            tsdm.jk,
            CASE
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) < 30 THEN '< 30 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 30 AND 39 THEN '30-39 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 40 AND 49 THEN '40-49 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) BETWEEN 50 AND 59 THEN '50-59 tahun'
                WHEN EXTRACT(YEAR FROM CURRENT_DATE) - EXTRACT(YEAR FROM tsdm.tgl_lahir) >= 60 THEN '> 60 tahun'
            END
        ORDER BY
            tsdm.jk ASC,
            kelompok_usia ASC;

        ");

        return $data;
    }

    public function getIkatanKerjaDosen()
    {
        $data = DB::select
        ("
                SELECT
            CASE
                WHEN rik.nm_ikatan_kerja IN ('Dokter Pendidik Klinis', 'Dosen dengan Perjanjian Kerja', 'Dosen PNS DPK', 'Dosen Tetap', 'Dosen Tetap BH',
                    'Dosen Tidak Tetap', 'Instruktur', 'JFT (Jabatan Fungsional Tertentu)', 'P3K ASN', 'Tutor') THEN rik.nm_ikatan_kerja
                ELSE 'Lainnya'
            END AS name,
            COUNT(DISTINCT tsdm.id_sdm) AS value
        FROM pdrd.sdm tsdm
        LEFT JOIN (
            SELECT
                treg.id_sdm,
                treg.id_reg_ptk,
                treg.id_sp,
                treg.id_jns_keluar,
                treg.id_sms,
                treg.id_stat_pegawai,
                treg.id_ikatan_kerja,
                ROW_NUMBER() OVER (PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.ikatan_kerja_sdm rik ON lreg.id_ikatan_kerja = rik.id_ikatan_kerja
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran = 2023
            AND tsdm.soft_delete = 0
            AND tsdm.id_jns_sdm = 12
            AND tsp.stat_sp = 'A'
            AND tsms.id_jns_sms = 3
            AND LEFT(tsp.id_wil, 2) <> '99'
            AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
            AND lreg.id_jns_keluar IS NULL
        GROUP BY rik.nm_ikatan_kerja;

        ");

        return $data;
    }

    // public function getJumGolonganDosen()
    // {
    //     $data = DB::select
    //     ("
        
    //     ");

    //     return $data;
    // }
    // public function getJumGolonganDosen()
    // {
    //     $data = DB::select
    //     ("
        
    //     ");

    //     return $data;
    // }
    // public function getJumGolonganDosen()
    // {
    //     $data = DB::select
    //     ("
        
    //     ");

    //     return $data;
    // }
    
}