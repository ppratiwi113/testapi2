<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiDashboard extends Model
{
    public function getLastUpdate()
    {
        $data = DB::select("
                SELECT MAX(last_update) AS latest_last_update
        FROM (
            SELECT last_update FROM pdrd.reg_ptk
            UNION
            SELECT last_update FROM pdrd.keaktifan_ptk
            UNION
            SELECT last_update FROM pdrd.satuan_pendidikan
            UNION
            SELECT last_update FROM pdrd.sms
            UNION
            SELECT last_update FROM pdrd.rwy_fungsional
            UNION
            SELECT last_update FROM pdrd.rwy_pend_formal
            UNION
            SELECT last_update FROM pdrd.rwy_sertifikasi
            UNION
            SELECT last_update FROM sdid.reg_serdos
            UNION
            SELECT last_update FROM sdid.lulus_serdos
            UNION
            SELECT last_update FROM sdid.serdik
            UNION
            SELECT last_update FROM sdid.usul_serdos_d1
        ) AS all_tables;
        ");

        return $data;
    }
    public function getJumlahDosen()
    {
        $data = DB::select("
            SELECT
            COALESCE(tahun_ini.id_stat_aktif, tahun_sebelumnya.id_stat_aktif) AS id_stat_aktif,
            COALESCE(tahun_ini.total, 0) AS total_dosen_tahun_ini,
            COALESCE(tahun_ini.last_update, tahun_sebelumnya.last_update) AS last_update,
            COALESCE(tahun_ini.total, 0) - COALESCE(tahun_sebelumnya.total, 0) AS peningkatan_dari_tahun_lalu
        FROM (
            SELECT
                tsdm.id_stat_aktif,
                COUNT(tsdm.id_sdm) AS total,
                MAX(tsdm.last_update) AS last_update
            FROM
                pdrd.sdm tsdm
            LEFT JOIN
                pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
            LEFT JOIN
                pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
            LEFT JOIN
                pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
            LEFT JOIN
                pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
            WHERE
                tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE) -- Menggunakan tahun sekarang
                AND tkeaktifan.a_sp_homebase = 1
                AND tsdm.soft_delete = 0
                AND tsdm.id_jns_sdm = 12
                AND tsp.stat_sp = 'A'
                AND tsms.id_jns_sms = 3
                AND LEFT(tsp.id_wil, 2) <> '99'
                AND tsdm.id_stat_aktif IN ('1')
                AND treg.id_jns_keluar IS NULL
            GROUP BY
                tkeaktifan.id_thn_ajaran, tsdm.id_stat_aktif
        ) AS tahun_ini
        LEFT JOIN (
            SELECT
                tsdm.id_stat_aktif,
                COUNT(tsdm.id_sdm) AS total,
                MAX(tsdm.last_update) AS last_update
            FROM
                pdrd.sdm tsdm
            LEFT JOIN
                pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
            LEFT JOIN
                pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
            LEFT JOIN
                pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
            LEFT JOIN
                pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
            WHERE
                tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE) - 1 -- Tahun sebelumnya
                AND tkeaktifan.a_sp_homebase = 1
                AND tsdm.soft_delete = 0
                AND tsdm.id_jns_sdm = 12
                AND tsp.stat_sp = 'A'
                AND tsms.id_jns_sms = 3
                AND LEFT(tsp.id_wil, 2) <> '99'
                AND tsdm.id_stat_aktif IN ('1')
                AND treg.id_jns_keluar IS NULL
            GROUP BY
                tkeaktifan.id_thn_ajaran, tsdm.id_stat_aktif
        ) AS tahun_sebelumnya ON tahun_ini.id_stat_aktif = tahun_sebelumnya.id_stat_aktif;    
        ");

        return $data;
    }

    public function getJumlahTendik() //diganti tahunnya
    {
        $data = DB::select("
        SELECT
            COALESCE(tahun_ini.total, 0) AS total_tendik_tahun_ini,
            COALESCE(tahun_ini.total, 0) - COALESCE(tahun_sebelumnya.total, 0) AS peningkatan_dari_tahun_lalu
        FROM (
            SELECT
                COUNT(tsdm.id_sdm) AS total
            FROM
                pdrd.sdm tsdm
            LEFT JOIN
                pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
            LEFT JOIN
                pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
            LEFT JOIN
                pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
            LEFT JOIN
                pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
            WHERE
                tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
                AND tkeaktifan.a_sp_homebase = 1
                AND tsdm.soft_delete = 0
                AND tsdm.id_jns_sdm = 13
                AND tsp.stat_sp = 'A'
                AND LEFT(tsp.id_wil, 2) <> '99'
                AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
                AND treg.id_jns_keluar IS NULL
        ) AS tahun_ini
        CROSS JOIN (
            SELECT
                COUNT(tsdm.id_sdm) AS total
            FROM
                pdrd.sdm tsdm
            LEFT JOIN
                pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
            LEFT JOIN
                pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
            LEFT JOIN
                pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
            LEFT JOIN
                pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
            WHERE
                tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE) - 1 -- Tahun sebelumnya
                AND tkeaktifan.a_sp_homebase = 1
                AND tsdm.soft_delete = 0
                AND tsdm.id_jns_sdm = 13
                AND tsp.stat_sp = 'A'
                AND LEFT(tsp.id_wil, 2) <> '99'
                AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
                AND treg.id_jns_keluar IS NULL
        ) AS tahun_sebelumnya;
        ");

        return $data;
    }

    public function getJabfungDosen()
    {
        $data = DB::select("
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

    public function getJaPenDosen() //masih error query
    {
        $data = DB::select("
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
        LEFT JOIN (
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

    public function getJaPenTendik() //masih error di query
    {
        $data = DB::select("
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

    public function getJumSerDosen() //diganti tahunnya
    {
        $data = DB::select("
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
            AND prs.thn_sert = EXTRACT(YEAR FROM CURRENT_DATE)
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
        $data = DB::select("
        SELECT
            CASE
                WHEN sls.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN sls.simpulan_akhir = 'T' THEN 'Tidak Lulus'
            END AS simpulan_akhir,
            COUNT(*) AS jumlah_sert
        FROM pdrd.rwy_sertifikasi AS prs
        JOIN sdid.reg_serdos AS sr ON sr.id_sdm = prs.id_sdm
        JOIN sdid.lulus_serdos AS sls ON sls.id_usul_dys = sr.id_usul_dys
        WHERE prs.thn_sert = EXTRACT(YEAR FROM CURRENT_DATE)
            AND prs.id_jns_sert IN (1, 2, 4)
            AND prs.soft_delete = 0
        GROUP BY simpulan_akhir;

        ");

        return $data;
    }

    public function getUsiaJekelDosen()
    {
        $data = DB::select("
                SELECT
        kelompok_usia AS usia,
            SUM(CAST(CASE WHEN jenis_kelamin = 'L' THEN jml_dosen ELSE 0 END AS INT)) AS value_lk,
            SUM(CAST(CASE WHEN jenis_kelamin = 'P' THEN jml_dosen ELSE 0 END AS INT)) AS value_pr
        FROM (
        SELECT
            tsdm.jk AS jenis_kelamin,
            CASE
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) < 30 THEN '< 30 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 30 AND 39 THEN '30-39 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 40 AND 49 THEN '40-49 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 50 AND 59 THEN '50-59 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) >= 60 THEN '> 60 tahun'
            END AS kelompok_usia,
            COUNT(DISTINCT treg.id_sdm) AS jml_dosen
        FROM pdrd.sdm tsdm
        LEFT JOIN pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
        LEFT JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        WHERE tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
        AND tkeaktifan.a_sp_homebase = 1
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 12
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN('1', '20', '24', '25', '27')
        AND treg.id_jns_keluar IS NULL
        GROUP BY
            tsdm.jk,
            CASE
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) < 30 THEN '< 30 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 30 AND 39 THEN '30-39 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 40 AND 49 THEN '40-49 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) BETWEEN 50 AND 59 THEN '50-59 tahun'
                WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tsdm.tgl_lahir)) >= 60 THEN '> 60 tahun'
            END
        ) AS SubQuery
        GROUP BY kelompok_usia
        ORDER BY kelompok_usia ASC;



        ");

        return $data;
    }

    public function getIkatanKerjaDosen()
    {
        $data = DB::select("
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
        WHERE pkp.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
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

    public function getJumGolonganDosen()
    {
        $data = DB::select("
        SELECT
            CASE
                WHEN rpg.kode_gol IN ('I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c',
                    'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e') THEN rpg.kode_gol
                ELSE 'Tidak Ada Kepangkatan'
            END AS golongan_kepangkatan,
            COUNT(DISTINCT prp.id_sdm) AS jml_dosen,
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
            ref.pangkat_golongan AS rpg ON ps.id_pangkat_gol = rpg.id_pangkat_gol
        WHERE
            ps.id_jns_sdm = 12
            AND ps.id_stat_aktif = 1
            AND ps.soft_delete = 0
            AND pkp.soft_delete = 0
            AND prp.soft_delete = 0
        GROUP BY
            rpg.kode_gol
        ORDER BY
            rpg.kode_gol;

        ");

        return $data;
    }

    public function getBentukPendDosen()
    {
        $data = DB::select("
        SELECT rbp.nm_bp AS name, COUNT(DISTINCT tsdm.id_sdm) AS value
        FROM pdrd.sdm tsdm
        LEFT JOIN (
            SELECT
                treg.id_sdm,
                treg.id_reg_ptk,
                treg.id_sp,
                treg.id_jns_keluar,
                treg.id_sms,
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.bentuk_pendidikan rbp ON tsp.id_bp = rbp.id_bp
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
            AND tsdm.soft_delete = 0
            AND tsdm.id_jns_sdm = 12
            AND tsp.stat_sp = 'A'
            AND tsms.id_jns_sms = 3
            AND LEFT(tsp.id_wil, 2) <> '99'
            AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
            AND lreg.id_jns_keluar IS NULL
        GROUP BY rbp.nm_bp;

        ");

        return $data;
    }

    public function getBentukPendTendik()
    {
        $data = DB::select("
        SELECT rbp.nm_bp AS name, COUNT(DISTINCT tsdm.id_sdm) AS value
        FROM pdrd.sdm tsdm
        LEFT JOIN (
            SELECT
                treg.id_sdm,
                treg.id_reg_ptk,
                treg.id_sp,
                treg.id_jns_keluar,
                treg.id_sms,
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.bentuk_pendidikan rbp ON tsp.id_bp = rbp.id_bp
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 13
        AND tsp.stat_sp = 'A'
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
        AND lreg.id_jns_keluar IS NULL
        GROUP BY rbp.nm_bp;
        ");

        return $data;
    }

    public function getJumPangkatDosen()
    {
        $data = DB::select("
        SELECT
            CASE
                WHEN rpg.nm_pangkat IN (
                    'Juru Muda', 'Juru Muda Tk. I', 'Juru', 'Juru Tk. I', 'Pengatur Muda', 'Pengatur Muda Tk. I', 'Pengatur',
                    'Pengatur Tk. I', 'Penata Muda', 'Penata Muda Tk. I', 'Penata', 'Penata Tk. I', 'Pembina', 'Pembina Tk. I',
                    'Pembina Utama Muda', 'Pembina Utama Madya', 'Pembina Utama'
                ) THEN rpg.nm_pangkat
                ELSE 'Tidak Ada Kepangkatan'
            END AS golongan_kepangkatan,
            COUNT(DISTINCT prp.id_sdm) AS jml_dosen,
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
            ref.pangkat_golongan AS rpg ON ps.id_pangkat_gol = rpg.id_pangkat_gol
        WHERE
            ps.id_jns_sdm = 12
            AND ps.id_stat_aktif = 1
            AND ps.soft_delete = 0
            AND pkp.soft_delete = 0
            AND prp.soft_delete = 0
        GROUP BY
            rpg.nm_pangkat
        ORDER BY
            rpg.nm_pangkat;

        ");

        return $data;
    }

    // public function getJenjPendDosen()
    // {
    //     $data = DB::select
    //     ("
    //     SELECT
    //         CASE
    //             WHEN rjd.nm_jenj_didik IN (
    //                 'D1', 'D2', 'D3', 'D4', 'Informal', 'Lainnya', 'Non formal', 'Profesi', 'S1',
    //                 'S2', 'S2 Terapan', 'S3', 'S3 Terapan', 'SMA / sederajat', 'Sp-1', 'Sp-2'
    //             ) THEN rjd.nm_jenj_didik
    //             ELSE 'Tanpa Jenjang'
    //         END AS jenjang_pendidikan,
    //         COUNT(DISTINCT prp.id_sdm) AS jml_tendik,
    //         MAX(ps.last_update) AS last_update
    //     FROM
    //         pdrd.reg_ptk AS prp
    //     INNER JOIN
    //         pdrd.keaktifan_ptk AS pkp ON prp.id_reg_ptk = pkp.id_reg_ptk
    //     INNER JOIN
    //         pdrd.sdm AS ps ON prp.id_sdm = ps.id_sdm
    //     INNER JOIN
    //         pdrd.rwy_pend_formal AS prpf ON prp.id_sdm = prpf.id_sdm
    //     INNER JOIN
    //         ref.jenjang_pendidikan AS rjd ON prpf.id_jenj_didik = rjd.id_jenj_didik
    //     WHERE
    //         ps.id_jns_sdm = 12
    //         AND ps.id_stat_aktif = 1
    //         AND ps.soft_delete = 0
    //         AND pkp.soft_delete = 0
    //         AND prp.soft_delete = 0
    //     GROUP BY
    //         rjd.nm_jenj_didik
    //     ORDER BY
    //         rjd.nm_jenj_didik;

    //     ");

    //     return $data;
    // }

    public function getJumPangkatTendik()
    {
        $data = DB::select("
        SELECT
            CASE
                WHEN rpg.nm_pangkat IN (
                    'Juru Muda','Juru Muda Tk. I','Juru','Juru Tk. I','Pengatur Muda','Pengatur Muda Tk. I','Pengatur',
                    'Pengatur Tk. I','Penata Muda','Penata Muda Tk. I','Penata','Penata Tk. I','Pembina','Pembina Tk. I',
                    'Pembina Utama Muda','Pembina Utama Madya','Pembina Utama'
                ) THEN rpg.nm_pangkat
                ELSE 'Tidak Ada Kepangkatan'
            END AS golongan_kepangkatan,
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
            ref.pangkat_golongan AS rpg ON ps.id_pangkat_gol = rpg.id_pangkat_gol
        WHERE
            ps.id_jns_sdm = 13
            AND ps.id_stat_aktif = 1
            AND ps.soft_delete = 0
            AND pkp.soft_delete = 0
            AND prp.soft_delete = 0
        GROUP BY
            rpg.nm_pangkat
        ORDER BY
            rpg.nm_pangkat;

        ");

        return $data;
    }

    public function getJumPangDosenperAhli()
    {
        $data = DB::select("
        SELECT
            CASE
                WHEN rpg.kode_gol IN ('IV/d', 'IV/e') THEN 'Ahli Utama'
                WHEN rpg.kode_gol IN ('IV/a', 'IV/b', 'IV/c') THEN 'Ahli Madya'
                WHEN rpg.kode_gol IN ('III/c', 'III/d') THEN 'Ahli Muda'
                WHEN rpg.kode_gol IN ('III/a', 'III/b') THEN 'Ahli Pertama'
                ELSE 'Tidak Ada Kepangkatan'
            END AS kategori_kepangkatan,
            CASE
                WHEN rpg.kode_gol IN ('IV/d', 'IV/e') THEN 'Golongan IV/d - IV/e'
                WHEN rpg.kode_gol IN ('IV/a', 'IV/b', 'IV/c') THEN 'Golongan IV/a - IV/c'
                WHEN rpg.kode_gol IN ('III/c', 'III/d') THEN 'Golongan III/c - III/d'
                WHEN rpg.kode_gol IN ('III/a', 'III/b') THEN 'Golongan III/a - III/b'
                ELSE 'Tidak Ada Kepangkatan'
            END AS detail_kepangkatan,
            COUNT(DISTINCT prp.id_sdm) AS jml_dosen,
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
            ref.pangkat_golongan AS rpg ON ps.id_pangkat_gol = rpg.id_pangkat_gol
        WHERE
            ps.id_jns_sdm = 12
            AND ps.id_stat_aktif = 1
            AND ps.soft_delete = 0
            AND pkp.soft_delete = 0
            AND prp.soft_delete = 0
        GROUP BY
            CASE
                WHEN rpg.kode_gol IN ('IV/d', 'IV/e') THEN 'Ahli Utama'
                WHEN rpg.kode_gol IN ('IV/a', 'IV/b', 'IV/c') THEN 'Ahli Madya'
                WHEN rpg.kode_gol IN ('III/c', 'III/d') THEN 'Ahli Muda'
                WHEN rpg.kode_gol IN ('III/a', 'III/b') THEN 'Ahli Pertama'
                ELSE 'Tidak Ada Kepangkatan'
            END,
            CASE
                WHEN rpg.kode_gol IN ('IV/d', 'IV/e') THEN 'Golongan IV/d - IV/e'
                WHEN rpg.kode_gol IN ('IV/a', 'IV/b', 'IV/c') THEN 'Golongan IV/a - IV/c'
                WHEN rpg.kode_gol IN ('III/c', 'III/d') THEN 'Golongan III/c - III/d'
                WHEN rpg.kode_gol IN ('III/a', 'III/b') THEN 'Golongan III/a - III/b'
                ELSE 'Tidak Ada Kepangkatan'
            END
        ORDER BY
            CASE
                WHEN rpg.kode_gol IN ('IV/d', 'IV/e') THEN 'Ahli Utama'
                WHEN rpg.kode_gol IN ('IV/a', 'IV/b', 'IV/c') THEN 'Ahli Madya'
                WHEN rpg.kode_gol IN ('III/c', 'III/d') THEN 'Ahli Muda'
                WHEN rpg.kode_gol IN ('III/a', 'III/b') THEN 'Ahli Pertama'
                ELSE 'Tidak Ada Kepangkatan'
            END;
        ");

        return $data;
    }

    public function getJumPTAktifPerProv()
    {
        $data = DB::select("
        SELECT 
            LEFT(psp.id_wil, 2) || '0000' AS kode_wilayah,
            rw.nm_wil AS provinsi,
            COUNT(psp.id_sp) AS satuan_pendidikan
        FROM pdrd.satuan_pendidikan AS psp
        LEFT JOIN ref.wilayah AS rw ON rw.id_wil = LEFT(psp.id_wil, 2) || '0000'
        WHERE psp.stat_sp = 'A'
            AND rw.id_level_wil = 1
            AND psp.soft_delete = 0
        GROUP BY LEFT(psp.id_wil, 2) || '0000', rw.nm_wil
        ORDER BY kode_wilayah;
        ");

        return $data;
    }

    public function getJumPTAktifperBentPend()
    {
        $data = DB::select("
        SELECT
            rbp.nm_bp AS bentuk_pendidikan,
            COUNT(DISTINCT psp.id_sp) AS jml_pt
        FROM pdrd.satuan_pendidikan AS psp
        LEFT JOIN ref.bentuk_pendidikan AS rbp ON psp.id_bp = rbp.id_bp 
        WHERE psp.stat_sp = 'A'
            AND psp.soft_delete = 0
        GROUP BY rbp.nm_bp
        ORDER BY jml_pt DESC;
        ");

        return $data;
    }

    public function getStatKepegawaianDosen()
    {
        $data = DB::select("
        SELECT 
            CASE WHEN rsk.nm_stat_pegawai = 'PNS' THEN 'PNS' ELSE 'NON PNS' END AS status_pegawai,
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
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.status_kepegawaian rsk ON lreg.id_stat_pegawai = rsk.id_stat_pegawai
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 12
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil,2) <> '99'
        AND tsdm.id_stat_aktif IN ('1','20','24','25','27')
        AND lreg.id_jns_keluar IS NULL
        GROUP BY CASE WHEN rsk.nm_stat_pegawai = 'PNS' THEN 'PNS' ELSE 'NON PNS' END;
        ");

        return $data;
    }

    public function getStatKepegawaianTendik()
    {
        $data = DB::select("
        SELECT 
            CASE WHEN rsk.nm_stat_pegawai = 'PNS' THEN 'PNS' ELSE 'NON PNS' END AS status_pegawai,
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
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.status_kepegawaian rsk ON lreg.id_stat_pegawai = rsk.id_stat_pegawai
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 13
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1','20','24','25','27')
        AND lreg.id_jns_keluar IS NULL
        GROUP BY CASE WHEN rsk.nm_stat_pegawai = 'PNS' THEN 'PNS' ELSE 'NON PNS' END;

        ");

        return $data;
    }

    public function getBKDJenis()
    {
        $data= DB::select
        ("
        SELECT 'bkd_ajar' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_bkd_ajar
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE)
        UNION
        SELECT 'bkd_didik' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_bkd_didik
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE)
        UNION
        SELECT 'bkd_lit' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_bkd_lit
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE)
        UNION
        SELECT 'bkd_pengmas' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_bkd_pengmas
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE)
        UNION
        SELECT 'bkd_tunjang' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_bkd_tunjang
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE)
        UNION
        SELECT 'Wajib Prof' AS tabel, COUNT(*) AS jumlah FROM sdid.klaim_wajib_prof
        WHERE EXTRACT(YEAR FROM last_update) = EXTRACT(YEAR FROM CURRENT_DATE);
        ");

        return $data;
    }

    public function getAjuanPerubahDataDosen()
    {
        $data = DB::select
        ("
        SELECT 'pdd_didik' AS tabel, COUNT(*) AS jumlah FROM sdid.ajuan_pdd_didik
        UNION ALL
        SELECT 'pdd_pokok' AS tabel, COUNT(*) AS jumlah FROM sdid.ajuan_pdd_pokok
        UNION ALL
        SELECT 'pdd_pangkat' AS tabel, COUNT(*) AS jumlah FROM sdid.ajuan_pdd_pangkat
        UNION ALL
        SELECT 'pdd_jabfung' AS tabel, COUNT(*) AS jumlah FROM sdid.ajuan_pdd_jabfung
        UNION ALL
        SELECT 'pdd_sertifikasi' AS tabel, COUNT(*) AS jumlah FROM sdid.ajuan_pdd_sertifikasi;

        ");

        return $data;
    }

    public function getLaporanBKD()
    {
        $data = DB::select
        ("
        SELECT 
            rj.nm_jabfung, 
            CASE
                WHEN snab.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN snab.simpulan_akhir IN ('T', 'X') THEN 'Tidak Lulus'
                ELSE 'Undefined'
            END AS simpulan_akhir,
            count(prf.id_sdm) as jumlah
        FROM pdrd.sdm tsdm 
        LEFT JOIN pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
        LEFT JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        LEFT JOIN sdid.nilai_bkd snb ON snb.id_reg_ptk = treg.id_reg_ptk AND snb.soft_delete = 0
        LEFT JOIN sdid.nilai_asesor_bkd snab ON snab.id_nilai_bkd = snb.id_nilai_bkd AND snab.soft_delete = 0
        LEFT JOIN pdrd.rwy_fungsional prf ON prf.id_rwy_jabfung = snb.id_rwy_jabfung AND prf.soft_delete = 0
        LEFT JOIN ref.jabfung rj ON rj.id_jabfung = prf.id_jabfung
        WHERE tkeaktifan.id_thn_ajaran = EXTRACT(YEAR FROM CURRENT_DATE)
        AND tkeaktifan.a_sp_homebase = 1
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 12
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1','20','24','25','27')
        AND treg.id_jns_keluar IS NULL
        AND snab.simpulan_akhir IS NOT NULL
        AND rj.nm_jabfung IS NOT NULL
        GROUP BY 
            rj.nm_jabfung, 
            CASE
                WHEN snab.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN snab.simpulan_akhir IN ('T', 'X') THEN 'Tidak Lulus'
                ELSE 'Undefined'
            END;

        ");

        return $data;
    }

    
    //Trend

    public function getTrendJumDosen()
    {
        $data = DB::select("
        SELECT
            nm_thn_ajaran,
            COUNT(DISTINCT tsdm.id_sdm) AS jml_dosen
        FROM pdrd.sdm tsdm
        LEFT JOIN pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
        LEFT JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        LEFT JOIN ref.tahun_ajaran rta ON tkeaktifan.id_thn_ajaran = rta.id_thn_ajaran
        WHERE tkeaktifan.a_sp_homebase = 1
            AND tsdm.soft_delete = 0
            AND tsdm.id_jns_sdm = 12
            AND tsp.stat_sp = 'A'
            AND tsms.id_jns_sms = 3
            AND LEFT(tsp.id_wil,2) <> '99'
            AND tsdm.id_stat_aktif IN('1','20','24','25','27')
            AND treg.id_jns_keluar IS NULL
            AND tkeaktifan.id_thn_ajaran BETWEEN (date_part('year', now())-3) and date_part('year', now())
        GROUP BY nm_thn_ajaran
        ORDER BY nm_thn_ajaran;
        ");

        return $data;
    }

    public function getTrendJumTendik()
    {
        $data = DB::select("
        SELECT
            nm_thn_ajaran,
            COUNT(DISTINCT tsdm.id_sdm) AS jml_tendik
        FROM pdrd.sdm tsdm
        LEFT JOIN pdrd.reg_ptk treg ON treg.id_sdm = tsdm.id_sdm AND treg.soft_delete = 0
        LEFT JOIN pdrd.keaktifan_ptk tkeaktifan ON tkeaktifan.id_reg_ptk = treg.id_reg_ptk AND tkeaktifan.soft_delete = 0
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = treg.id_sp AND tsp.soft_delete = 0
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = treg.id_sms AND tsms.soft_delete = 0
        LEFT JOIN ref.tahun_ajaran rta ON tkeaktifan.id_thn_ajaran = rta.id_thn_ajaran
        WHERE tkeaktifan.a_sp_homebase = 1
            AND tsdm.soft_delete = 0
            AND tsdm.id_jns_sdm = 13
            AND tsp.stat_sp = 'A'
            AND tsms.id_jns_sms = 3
            AND LEFT(tsp.id_wil,2) <> '99'
            AND tsdm.id_stat_aktif IN('1','20','24','25','27')
            AND treg.id_jns_keluar IS NULL
            AND tkeaktifan.id_thn_ajaran BETWEEN (date_part('year', now())-3) and date_part('year', now())
        GROUP BY nm_thn_ajaran
        ORDER BY nm_thn_ajaran;
        ");

        return $data;
    }

    public function getTrendBentukPend()
    {
        $data = DB::select("
        SELECT
            rbp.nm_bp AS name,
            pkp.id_thn_ajaran,
            COUNT(DISTINCT tsdm.id_sdm) AS value
        FROM pdrd.sdm tsdm
        LEFT JOIN (
            SELECT
                treg.id_sdm,
                treg.id_reg_ptk,
                treg.id_sp,
                treg.id_jns_keluar,
                treg.id_sms,
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk treg
        ) lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.bentuk_pendidikan rbp ON tsp.id_bp = rbp.id_bp
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran BETWEEN (date_part('year', now())-3) and date_part('year', now())
            AND tsdm.soft_delete = 0
            AND tsdm.id_jns_sdm = 12
            AND tsp.stat_sp = 'A'
            AND tsms.id_jns_sms = 3
            AND LEFT(tsp.id_wil, 2) <> '99'
            AND tsdm.id_stat_aktif IN('1','20','24','25','27')
            AND lreg.id_jns_keluar IS NULL
        GROUP BY rbp.nm_bp, pkp.id_thn_ajaran
        ORDER BY rbp.nm_bp, pkp.id_thn_ajaran;
        ");

        return $data;
    }

    public function getTrendSertDosen() //NIDN-NIDK
    {
        $data = DB::select("
        SELECT 
    prs.thn_sert AS tahun,
    COUNT(DISTINCT CASE WHEN LEFT(ps.nidn, 2) BETWEEN '00' AND '87' THEN prs.id_sdm END) AS value_nidn,
    COUNT(DISTINCT CASE WHEN LEFT(ps.nidn, 2) BETWEEN '88' AND '99' THEN prs.id_sdm END) AS value_nidk
FROM pdrd.rwy_sertifikasi AS prs
JOIN pdrd.sdm AS ps ON prs.id_sdm = ps.id_sdm AND ps.soft_delete = 0
WHERE prs.id_jns_sert IN (1, 2, 4)
AND prs.thn_sert BETWEEN 2020 AND 2023
AND prs.soft_delete = 0
AND LEFT(ps.nidn, 2) BETWEEN '00' AND '99'
GROUP BY prs.thn_sert
ORDER BY prs.thn_sert;
        ");

        return $data;
    }

    public function getTrendSertDosenLulusTdkLulus() //2020-2023(Sekarang)
    {
        $data = DB::select("
        SELECT
            prs.thn_sert,
         CASE
                WHEN sls.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN sls.simpulan_akhir = 'T' THEN 'Tidak Lulus'
            END AS simpulan_akhir_in,
            COUNT(*) AS jumlah_sert
        FROM pdrd.rwy_sertifikasi prs
        JOIN sdid.reg_serdos sr ON sr.id_sdm = prs.id_sdm AND sr.soft_delete = 0
        JOIN sdid.lulus_serdos sls ON sls.id_usul_dys = sr.id_usul_dys AND sls.soft_delete = 0
        WHERE prs.thn_sert BETWEEN (date_part('year', now())-3) and date_part('year', now())
        AND prs.soft_delete = 0
        AND prs.id_jns_sert IN (1,2,4)
        GROUP BY 
            prs.thn_sert, 
            CASE
                WHEN sls.simpulan_akhir = 'L' THEN 'Lulus'
                WHEN sls.simpulan_akhir = 'T' THEN 'Tidak Lulus'
            END
        ORDER BY prs.thn_sert;
        ");

        return $data;
    }

    public function getTrendUsulanSerdos() //2020-2023
    {
        $data = DB::select("
        select 
            CAST(a.tahun_sert AS VARCHAR) AS tahun_sert,
            SUM(CAST(a.tidak_diajukan AS INT)) AS tidak_diajukan,
            SUM(CAST(a.diajukan AS INT)) AS diajukan
        from (
        select 
            sps.tahun_sert,
            sps.id_periode_sert,
            SUM(case when rs.a_diajukan = 0 then 1 else 0 end) as tidak_diajukan,
            SUM(case when rs.a_diajukan = 1 then 1 else 0 end) as diajukan
        from ref.periode_sert sps
        join sdid.reg_serdos rs on rs.id_periode_sert = sps.id_periode_sert and rs.soft_delete = 0
        where sps.expired_date is null
        and sps.tahun_sert between (date_part('year', now())-3) and date_part('year', now())
        group by sps.tahun_sert, sps.id_periode_sert 
        )as a
        group by a.tahun_sert
        ");

        return $data;
    }

    public function getTrendStatKepegawaian()
    {
        $data = DB::select("
        SELECT 
            pkp.id_thn_ajaran AS tahun,
            COUNT(DISTINCT CASE WHEN rsk.nm_stat_pegawai = 'PNS' THEN tsdm.id_sdm END) AS value_pns,
            COUNT(DISTINCT CASE WHEN rsk.nm_stat_pegawai <> 'PNS' THEN tsdm.id_sdm END) AS value_non_pns
        FROM pdrd.sdm AS tsdm
        LEFT JOIN (
            SELECT
                treg.id_sdm,
                treg.id_reg_ptk,
                treg.id_sp,
                treg.id_jns_keluar,
                treg.id_sms,
                treg.id_stat_pegawai,
                treg.id_ikatan_kerja,
                ROW_NUMBER() OVER(PARTITION BY treg.id_sdm ORDER BY treg.last_update DESC) AS rn
            FROM pdrd.reg_ptk AS treg
        ) AS lreg ON tsdm.id_sdm = lreg.id_sdm AND lreg.rn = 1
        LEFT JOIN pdrd.satuan_pendidikan AS tsp ON tsp.id_sp = lreg.id_sp
        LEFT JOIN pdrd.keaktifan_ptk AS pkp ON pkp.id_reg_ptk = lreg.id_reg_ptk
        LEFT JOIN ref.status_kepegawaian AS rsk ON lreg.id_stat_pegawai = rsk.id_stat_pegawai
        LEFT JOIN pdrd.sms tsms ON tsms.id_sms = lreg.id_sms AND tsms.soft_delete = 0
        WHERE pkp.id_thn_ajaran BETWEEN 2020 AND 2023
        AND tsdm.soft_delete = 0
        AND tsdm.id_jns_sdm = 12
        AND tsp.stat_sp = 'A'
        AND tsms.id_jns_sms = 3
        AND LEFT(tsp.id_wil, 2) <> '99'
        AND tsdm.id_stat_aktif IN ('1', '20', '24', '25', '27')
        AND lreg.id_jns_keluar IS NULL
        GROUP BY pkp.id_thn_ajaran
        ORDER BY pkp.id_thn_ajaran;
        ");

        return $data;
    }


    // public function getTrendPAKDosen() //2020-2023(Sekarang)
    // {
    //     $data = DB::select
    //     ("
    //     ");

    //     return $data;
    // }

}