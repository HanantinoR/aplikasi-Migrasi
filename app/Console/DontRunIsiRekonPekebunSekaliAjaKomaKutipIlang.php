<?php
date_default_timezone_set('Asia/Jakarta');
$servername_smart_psr = "127.0.0.1";
$username_smart_psr = "phpmyadmin";
$password_smart_psr = "Sucofindo123";
$dbname_smart_psr = "psr2";

$servername_psr_online = "127.0.0.1";
$username_psr_online = "phpmyadmin";
$password_psr_online = "Sucofindo123";
$dbname_psr_online = "psr";

$servername_rdp = "127.0.0.1";
$username_rdp = "phpmyadmin";
$password_rdp = "Sucofindo123";
$dbname_rdp = "rdp";

$waktu_awal = date('Y-m-d H:i:s');
$gagal = 0;
$berhasil = 0;

// Create connection
$conn_smart_psr = new mysqli($servername_smart_psr, $username_smart_psr, $password_smart_psr, $dbname_smart_psr);
// Check connection
if ($conn_smart_psr->connect_error) {
    die("Connection failed: " . $conn_smart_psr->connect_error);
}

// Create connection
$conn_psr_online = new mysqli($servername_psr_online, $username_psr_online, $password_psr_online, $dbname_psr_online);
// Check connection
if ($conn_psr_online->connect_error) {
    die("Connection failed: " . $conn_psr_online->connect_error);
}

// Create connection
$conn_rdp = new mysqli($servername_rdp, $username_rdp, $password_rdp, $dbname_rdp);
// Check connection
if ($conn_rdp->connect_error) {
    die("Connection failed: " . $conn_rdp->connect_error);
}
$user = 'Script Migrasi dan Auto Rekon Pekebun';
$query_get_proposal_rekon = 'SELECT * FROM lookup_proposal_kelembagaan_pekebun WHERE id_proposal_smart_psr IS NOT NULL ORDER BY nomor_proposal ASC';
$hasil_get_proposal_rekon = mysqli_query($conn_rdp, $query_get_proposal_rekon);

$array_id_proposal_psr = array();
$array_id_proposal_smart_psr = array();

$array_lookup_by_id_proposal = array();

foreach ($hasil_get_proposal_rekon as $key => $row) {
    array_push($array_id_proposal_psr, $row['id_proposal_psr_online']);
    array_push($array_id_proposal_smart_psr, $row['id_proposal_smart_psr']);
    $array_lookup_by_id_proposal[$row['id_proposal_psr_online']] = array(
        "id_proposal_psr_online" => $row['id_proposal_psr_online'],
        "id_proposal_smart_psr" => $row['id_proposal_smart_psr'],
        "id_kelembagaan_pekebun_psr_online" => $row['id_kelembagaan_pekebun_psr_online'],
        "id_kelembagaan_pekebun_smart_psr" => $row['id_kelembagaan_pekebun_smart_psr'],
        "nomor_proposal" => $row['nomor_proposal'],
    );
}
// var_dump($array_lookup_by_id_proposal);

$string_array_id_proposal_psr = implode(",", $array_id_proposal_psr);
$string_array_id_proposal_smart_psr = implode(",", $array_id_proposal_smart_psr);

$query_get_pekebun_udah_rekon = 'SELECT id_pekebun_psr_online FROM `lookup_pekebun_proposal` GROUP BY id_pekebun_psr_online ORDER BY id_pekebun_psr_online ASC;';
$hasil_get_pekebun_udah_rekon = mysqli_query($conn_rdp, $query_get_pekebun_udah_rekon);
$array_pekebun_udah_rekon = array();
foreach ($hasil_get_pekebun_udah_rekon as $key => $value) {
    array_push($array_pekebun_udah_rekon,$value['id_pekebun_psr_online']);
}
$string_array_id_pekebun_udah_rekon = implode(",", $array_pekebun_udah_rekon);
// var_dump($string_array_id_pekebun_udah_rekon);
// exit;

$query_get_pekebun_proposal_psr_online = 'SELECT * FROM tb_pekebun WHERE id_proposal IN (' . $string_array_id_proposal_psr . ') AND id_pekebun NOT IN ('.$string_array_id_pekebun_udah_rekon.') ORDER BY id_proposal';
// var_dump($query_get_pekebun_proposal_psr_online);
// exit;

//Jalankan Query CekMT940PenyaluranBPDP sama Biar Gatokinnya Enak Jadi Engga Lemodh Query Group No Proposalnya Maszeh
$hasil_cek_get_pekebun_proposal_psr_online = mysqli_query($conn_psr_online, $query_get_pekebun_proposal_psr_online);

$array_pekebun = array();
$list_proposal_data_lookup_lengkap = array();
$total_data_pekebun = $hasil_cek_get_pekebun_proposal_psr_online->num_rows;
$array_hasil_cek_get_pekebun_proposal_psr_online = mysqli_fetch_all($hasil_cek_get_pekebun_proposal_psr_online, MYSQLI_ASSOC);

foreach ($array_hasil_cek_get_pekebun_proposal_psr_online as $key => $row) {
    $now = date('Y-m-d H:i:s');
    if ($array_lookup_by_id_proposal[$row['id_proposal']] === null) {
        echo ("Id Proposal " . $row['id_proposal'] . " Tidak Rekon! \n Data Disekip Untuk Rekon... \n");
        continue;
    } else {
        $array_hasil_cek_get_pekebun_proposal_psr_online[$key]['id_proposal_smart_psr'] = $array_lookup_by_id_proposal[$row['id_proposal']]['id_proposal_smart_psr'];
        $array_hasil_cek_get_pekebun_proposal_psr_online[$key]['id_kelembagaan_pekebun_smart_psr'] = $array_lookup_by_id_proposal[$row['id_proposal']]['id_kelembagaan_pekebun_smart_psr'];
        $row['id_proposal_smart_psr'] = $array_lookup_by_id_proposal[$row['id_proposal']]['id_proposal_smart_psr'];
        $row['id_kelembagaan_pekebun_smart_psr'] = $array_lookup_by_id_proposal[$row['id_proposal']]['id_kelembagaan_pekebun_smart_psr'];
        $row['no_dokumen'] = $array_lookup_by_id_proposal[$row['id_proposal']]['nomor_proposal'];

        $row['nama_pekebun'] = str_replace("'", "\'", $row['nama_pekebun']);
        $row['nama_pekebun'] = str_replace(",", "\,", $row['nama_pekebun']);
        $row['nama_pekebun'] = str_replace('"', '\"', $row['nama_pekebun']);

        $query_get_pekebun_proposal_smart_psr = 'SELECT id,id_proposal,nik,nama_pekebun,luas_hektar FROM tbl_master_pekebun WHERE id_proposal = "' . $row['id_proposal_smart_psr'] . '" AND nik = "' . $row['no_ktp'] . '" ORDER BY id_proposal ASC LIMIT 1';
        $hasil_cek_get_pekebun_proposal_smart_psr = mysqli_query($conn_smart_psr, $query_get_pekebun_proposal_smart_psr);

        if ($hasil_cek_get_pekebun_proposal_smart_psr->num_rows == 0) {
            echo ($row['nama_pekebun'] . " di Nomor Proposal " . $row['no_dokumen'] . " Tidak Ditemukan Data Pekebun Rekon SMART-PSR, Pekebun di Skip... \n");
            $gagal++;
        } else {
            $id_pekebun_smart_psr = mysqli_fetch_assoc($hasil_cek_get_pekebun_proposal_smart_psr)['id'];
            $query_get_data_kelembagaan_pekebun_psr_online = 'SELECT * FROM tb_koperasi WHERE id_koperasi = "' . $row['id_koperasi'] . '" ORDER BY id_koperasi ASC LIMIT 1';
            $hasil_get_data_kelembagaan_pekebun_psr_online = mysqli_query($conn_psr_online, $query_get_data_kelembagaan_pekebun_psr_online);

            if ($hasil_get_data_kelembagaan_pekebun_psr_online->num_rows >= 1) {
                $array_data_kelembagaan_pekebun = mysqli_fetch_assoc($hasil_get_data_kelembagaan_pekebun_psr_online);
            } else {
                echo ('Kelembagaan Pekebun Proposal ' . $row['no_dokumen'] . "Tidak Ditemukan! \n Data Disekip Untuk Rekon... \n");
                continue;
            }
            unset($hasil_get_data_kelembagaan_pekebun_psr_online);

            @$array_data_kelembagaan_pekebun['koperasi'] = str_replace("'", "\'", $array_data_kelembagaan_pekebun['koperasi']);
            @$array_data_kelembagaan_pekebun['koperasi'] = str_replace(",", "\,", $array_data_kelembagaan_pekebun['koperasi']);
            @$array_data_kelembagaan_pekebun['koperasi'] = str_replace('"', '\"', $array_data_kelembagaan_pekebun['koperasi']);

            $cek_terdaftar_lembaga_pekebun = 'SELECT * FROM kelembagaan_pekebun WHERE nama_kelembagaan_pekebun = "' . trim($array_data_kelembagaan_pekebun['koperasi']) . '" AND provinsi_kelembagaan_pekebun = "' . trim($array_data_kelembagaan_pekebun['provinsi']) . '" AND kota_kabupaten_kelembagaan_pekebun = "' . trim($array_data_kelembagaan_pekebun['kabupaten']) . '" LIMIT 1;';
            $hasil_cek_terdaftar_lembaga_pekebun = mysqli_query($conn_rdp, $cek_terdaftar_lembaga_pekebun);

            if ($hasil_cek_terdaftar_lembaga_pekebun->num_rows >= 1) {
                $id_kelembagaan_pekebun = mysqli_fetch_assoc($hasil_cek_terdaftar_lembaga_pekebun)['id'];
            } else {
                echo ("Memigrasikan Data LP " . $array_data_kelembagaan_pekebun['koperasi'] . " \n");
                $tgl_terbit_legalitas = @trim($array_data_kelembagaan_pekebun['tgl_terbit_legalitas']);
                if ($tgl_terbit_legalitas === '0000-00-00' || $tgl_terbit_legalitas === "" || $tgl_terbit_legalitas === null) {
                    $tgl_terbit_legalitas = "NULL";
                } else {
                    $tgl_terbit_legalitas = '"' . $tgl_terbit_legalitas . '"';
                }
                @$array_data_kelembagaan_pekebun['alamat'] = str_replace("'", "\'", $array_data_kelembagaan_pekebun['alamat']);
                @$array_data_kelembagaan_pekebun['alamat'] = str_replace(",", "\,", $array_data_kelembagaan_pekebun['alamat']);
                @$array_data_kelembagaan_pekebun['alamat'] = str_replace('"', '\"', $array_data_kelembagaan_pekebun['alamat']);

                @$array_data_kelembagaan_pekebun['no_legalitas_koperasi'] = str_replace("'", "\'", $array_data_kelembagaan_pekebun['no_legalitas_koperasi']);
                @$array_data_kelembagaan_pekebun['no_legalitas_koperasi'] = str_replace(",", "\,", $array_data_kelembagaan_pekebun['no_legalitas_koperasi']);
                @$array_data_kelembagaan_pekebun['no_legalitas_koperasi'] = str_replace('"', '\"', $array_data_kelembagaan_pekebun['no_legalitas_koperasi']);


                $query_daftar_kelembagaan_pekebun = 'insert into `kelembagaan_pekebun`
                (
                    `nama_kelembagaan_pekebun`,
                    `provinsi_kelembagaan_pekebun`,
                    `kota_kabupaten_kelembagaan_pekebun`,
                    `kecamatan_kelembagaan_pekebun`,
                    `kelurahan_kelembagaan_pekebun`,
                    `kodepos_kelembagaan_pekebun`,
                    `alamat_kelembagaan_pekebun`,
                    `nomor_telepon_kelembagaan_pekebun`,
                    `nomor_hp_kelembagaan_pekebun`,
                    `email_kelembagaan_pekebun`,
                    `jenis_kelembagaan_pekebun`,
                    `file_dokumen_legalitas_kelembagaan_pekebun`,
                    `nama_dokumen_legalitas_kelembagaan_pekebun`,
                    `nomor_dokumen_legalitas_kelembagaan_pekebun`,
                    `instansi_pengesahan_dokumen_legalitas_kelembagaan_pekebun`,
                    `tanggal_terbit_dokumen_legalitas_kelembagaan_pekebun`,
                    `titik_koordinat_kantor_kelembagaan_pekebun`,
                    `logo_kelembagaan_pekebun`,
                    `nama_ketua_kelembagaan_pekebun`,
                    `jenis_kelamin_ketua_kelembagaan_pekebun`,
                    `tempat_lahir_ketua_kelembagaan_pekebun`,
                    `tanggal_lahir_ketua_kelembagaan_pekebun`,
                    `nik_ketua_kelembagaan_pekebun`,
                    `kk_ketua_kelembagaan_pekebun`,
                    `status_pernikahan_ketua_kelembagaan_pekebun`,
                    `nomor_telepon_ketua_kelembagaan_pekebun`,
                    `nomor_hp_ketua_kelembagaan_pekebun`,
                    `provinsi_ketua_kelembagaan_pekebun`,
                    `kota_kabupaten_ketua_kelembagaan_pekebun`,
                    `kecamatan_ketua_kelembagaan_pekebun`,
                    `kelurahan_ketua_kelembagaan_pekebun`,
                    `kodepos_ketua_kelembagaan_pekebun`,
                    `alamat_ketua_kelembagaan_pekebun`,
                    `foto_ketua_kelembagaan_pekebun`,
                    `foto_ktp_ketua_kelembagaan_pekebun`,
                    `file_dokumen_penunjukkan_ketua_kelembagaan_pekebun`,
                    `nama_dokumen_penunjukkan_ketua_kelembagaan_pekebun`,
                    `nomor_dokumen_penunjukkan_ketua_kelembagaan_pekebun`,
                    `tanggal_terbit_dokumen_penunjukkan_ketua_kelembagaan_pekebun`,
                    `nama_pic_kelembagaan_pekebun`,
                    `nomor_pic_kelembagaan_pekebun`,
                    `created_at`,
                    `created_by`,
                    `updated_at`,
                    `updated_by`,
                    `deleted_at`,
                    `foto_kk_ketua_kelembagaan_pekebun`
                ) values (
                    "' . @trim($array_data_kelembagaan_pekebun['koperasi']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['provinsi']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['kabupaten']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['kecamatan']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['kelurahan']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['kodepos']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['alamat']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['tlp']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['hp']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['email']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['jenis_kelembagaan']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['legalitas']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['legalitas']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['no_legalitas_koperasi']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['notaris']) . '",
                    ' . $tgl_terbit_legalitas . ',
                    NULL,
                    "' . @trim($array_data_kelembagaan_pekebun['foto']) . '",
                    "' . @trim($array_data_kelembagaan_pekebun['pimpinan']) . '",
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    "' . $now . '",
                    "' . $user . '",
                    "' . $now . '",
                    "' . $user . '",
                    NULL,
                    NULL
                )';
                mysqli_query($conn_rdp, $query_daftar_kelembagaan_pekebun);

                $id_kelembagaan_pekebun = mysqli_insert_id($conn_rdp);
            }

            unset($hasil_cek_terdaftar_lembaga_pekebun);
            unset($array_data_kelembagaan_pekebun);

            $query_get_data_proposal_psr_online = 'SELECT * FROM tb_proposal WHERE id_proposal = "' . $row['id_proposal'] . '" ORDER BY id_proposal ASC LIMIT 1';
            $hasil_get_data_proposal_psr_online = mysqli_query($conn_psr_online, $query_get_data_proposal_psr_online);

            if ($hasil_get_data_proposal_psr_online->num_rows >= 1) {
                $array_data_proposal_psr_online = mysqli_fetch_assoc($hasil_get_data_proposal_psr_online);
            } else {
                echo ('Proposal ' . $row['no_dokumen'] . "Tidak Ditemukan! \n Data Disekip Untuk Rekon... \n");
                continue;
            }
            unset($hasil_get_data_proposal_psr_online);

            $cek_terdaftar_proposal = 'SELECT * FROM proposal WHERE nomor_proposal = "' . trim($array_data_proposal_psr_online['no_dokumen']) . '" LIMIT 1;';
            $hasil_cek_terdaftar_proposal = mysqli_query($conn_rdp, $cek_terdaftar_proposal);

            if ($hasil_cek_terdaftar_proposal->num_rows >= 1) {
                $id_proposal = mysqli_fetch_assoc($hasil_cek_terdaftar_proposal)['id'];
            } else {
                $query_daftar_proposal = 'insert into `proposal` (
                    `id_kelembagaan_pekebun`,
                    `nomor_proposal`,
                    `tanggal_pengajuan_proposal`,
                    `jalur_pengajuan`,
                    `status_lahan`,
                    `bank_mitra`,
                    `cabang_bank_mitra`,
                    `rencana_anggaran_belanja`,
                    `produktifitas_tanaman`,
                    `tahun_tanam_tanaman`,
                    `luas_lahan_diajukan`,
                    `luas_lahan_didanai`,
                    `luas_lahan_dikembalikan`,
                    `status_verifikasi_dokumen`,
                    `tanggal_verifikasi_dokumen`,
                    `verifikator_verifikasi_dokumen`,
                    `keterangan_verifikasi_dokumen`,
                    `created_at`,
                    `created_by`,
                    `updated_at`,
                    `updated_by`,
                    `step`,
                    `status_push_verifikator`
                ) values (
                    ' . $id_kelembagaan_pekebun . ',
                    "' . @trim($array_data_proposal_psr_online['no_dokumen']) . '",
                    "' . date('Y-m-d', strtotime($array_data_proposal_psr_online['tgl_input'])) . '",
                    "' . @trim($array_data_proposal_psr_online['jalur']) . '",
                    NULL,
                    "' . @trim($array_data_proposal_psr_online['nama_bank']) . '",
                    "' . @trim($array_data_proposal_psr_online['cabang_bank_proposals']) . '",
                    NULL,
                    ' . ($array_data_proposal_psr_online['produktivitas_tanaman'] == null ? 0.0000 : str_replace(",", ".", $array_data_proposal_psr_online['produktivitas_tanaman'])) . ',
                    "' . @trim($array_data_proposal_psr_online['tahun_tanaman']) . '",
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    "' . $now . '",
                    "' . $user . '",
                    "' . $now . '",
                    "' . $user . '",
                    NULL,
                    NULL
                )';

                // var_dump($query_daftar_proposal);

                mysqli_query($conn_rdp, $query_daftar_proposal);

                $id_proposal = mysqli_insert_id($conn_rdp);
            }
            unset($array_data_proposal_psr_online);

            $query_cek_nik_udah_kedaftar_apa_belom = 'SELECT * FROM pekebun where nik_pekebun = "' . $row['no_ktp'] . '" LIMIT 1';
            $hasil_cek_nik_udah_kedaftar_apa_belom = mysqli_query($conn_rdp, $query_cek_nik_udah_kedaftar_apa_belom);
            if ($hasil_cek_nik_udah_kedaftar_apa_belom->num_rows >= 1) {
                $id_pekebun = mysqli_fetch_assoc($hasil_cek_nik_udah_kedaftar_apa_belom)['id'];
            } else {
                $check_tanggal_lahir_pekebun = $row['tgl_lahir'];
                if ($check_tanggal_lahir_pekebun === null || $check_tanggal_lahir_pekebun == '0000-00-00') {
                    $tanggal_lahir_pekebun = "NULL";
                } else {
                    $tanggal_lahir_pekebun = "'" . $row['tgl_lahir'] . "'";
                }

                // var_dump($tanggal_lahir_pekebun);
                $query_daftar_pekebun = 'insert into `pekebun` (
                `nik_pekebun`,
                `kk_pekebun`,
                `nama_pekebun`,
                `tanggal_lahir_pekebun`,
                `jenis_kelamin_pekebun`,
                `status_pernikahan_pekebun`,
                `nomor_hp_pekebun`,
                `provinsi_pekebun`,
                `kota_kabupaten_pekebun`,
                `kecamatan_pekebun`,
                `kelurahan_pekebun`,
                `kodepos_pekebun`,
                `alamat_pekebun`,
                `luas_lahan_tersedia`,
                `created_at`,
                `created_by`,
                `updated_at`,
                `updated_by`
            ) values (
                "' . @(trim($row['no_ktp']) == null ? null : trim($row['no_ktp'])) . '",
                "' . @(trim($row['no_kk']) == null ? null : trim($row['no_kk'])) . '",
                "' . @(trim($row['nama_pekebun']) == null ? null : trim($row['nama_pekebun'])) . '",
                ' . $tanggal_lahir_pekebun . ',
                "' . @(trim($row['jenis_kelamin']) == null ? null : trim($row['jenis_kelamin'])) . '",
                "' . @(trim($row['status_pernikahan']) == null ? null : trim($row['status_pernikahan'])) . '",
                "' . @(trim($row['hp_pekebun']) == null ? null : trim($row['hp_pekebun'])) . '",
                "' . @(trim($row['provinsi']) == null ? null : trim($row['provinsi'])) . '",
                "' . @(trim($row['kabupaten']) == null ? null : trim($row['kabupaten'])) . '",
                "' . @(trim($row['kecamatan']) == null ? null : trim($row['kecamatan'])) . '",
                "' . @(trim($row['kelurahan']) == null ? null : trim($row['kelurahan'])) . '",
                "' . @(trim($row['kodepos']) == null ? null : trim($row['kodepos'])) . '",
                "' . @(trim($row['alamat_pekebun']) == null ? null : trim($row['alamat_pekebun'])) . '",
                4.0000,
                "' . $now . '",
                "' . $user . '",
                "' . $now . '",
                "' . $user . '"
                )';

                mysqli_query($conn_rdp, $query_daftar_pekebun);

                $id_pekebun = mysqli_insert_id($conn_rdp);
            }

            $query_daftar_lookup_pekebun_proposal = 'insert into `lookup_pekebun_proposal` (
                                                                                        `nomor_proposal`,
                                                                                        `id_proposal_psr_online`,
                                                                                        `id_proposal_smart_psr`,
                                                                                        `id_pekebun_psr_online`,
                                                                                        `id_pekebun_smart_psr`,
                                                                                        `created_at`,
                                                                                        `created_by`,
                                                                                        `id_proposal_rdp`,
                                                                                        `id_kelembagaan_pekebun_rdp`,
                                                                                        `id_pekebun_rdp`
                                                                                    ) values (
                                                                                        "' . $row['no_dokumen'] . '",
                                                                                        "' . $row['id_proposal'] . '",
                                                                                        "' . $row['id_proposal_smart_psr'] . '",
                                                                                        "' . $row['id_pekebun'] . '",
                                                                                        "' . $id_pekebun_smart_psr . '",
                                                                                        "' . $now . '",
                                                                                        "' . $user . '",
                                                                                        "' . $id_proposal . '",
                                                                                        "' . $id_kelembagaan_pekebun . '",
                                                                                        "' . $id_pekebun . '"
                                                                                    )';

            mysqli_query($conn_rdp, $query_daftar_lookup_pekebun_proposal);

            $id_lookup_pekebun_proposal = mysqli_insert_id($conn_rdp);

            $query_daftar_pekebunnya_proposal = 'insert into `pekebunnya_proposal` (
                                                `id_kelembagaan_pekebun`,
                                                `id_proposal`,
                                                `id_pekebun`,
                                                `created_at`,
                                                `created_by`
                                            ) values (
                                                "' . $id_kelembagaan_pekebun . '",
                                                "' . $id_proposal . '",
                                                "' . $id_pekebun . '",
                                                "' . $now . '",
                                                "' . $user . '"
                                                )';

            mysqli_query($conn_rdp, $query_daftar_pekebunnya_proposal);

            $id_pekebunnya_proposal = mysqli_insert_id($conn_rdp);

            $query_daftar_pekebunnya_kelembagaan = 'insert into `pekebunnya_kelembagaan` (
                                                    `id_kelembagaan_pekebun`,
                                                    `id_pekebun`,
                                                    `created_at`,
                                                    `created_by`
                                                    ) values (
                                                        "' . $id_kelembagaan_pekebun . '",
                                                        "' . $id_pekebun . '",
                                                        "' . $now . '",
                                                        "' . $user . '"
                                                        )';

            mysqli_query($conn_rdp, $query_daftar_pekebunnya_kelembagaan);

            $id_pekebunnya_kelembagaan = mysqli_insert_id($conn_rdp);

            $query_insert_log_migrasi_pekebun = 'insert into `pekebun_log` (
                                                `id_pekebun`,
                                                `log`,
                                                `created_at`,
                                                `created_by`
                                                ) values (
                                                "' . $id_pekebun . '",
                                                "Data Pekebun ' .$row['nama_pekebun'].' NIK '. $row['no_ktp'] . ' - ' . $row['no_dokumen']. ', berhasil di migrasi oleh ' . $user . ' melalui aplikasi migrasi RDP",
                                                "' . $now . '",
                                                "' . $user . '"
                                                )';

            mysqli_query($conn_rdp, $query_insert_log_migrasi_pekebun);

            $id_log_migrasi_pekebun = mysqli_insert_id($conn_rdp);

            $query_get_data_legalitas_lahan_psr_online = 'SELECT * FROM tb_legalitas WHERE id_pekebun = ' . $row['id_pekebun'] . ' ORDER BY id_legalitas ASC';

            $hasil_get_data_legalitas_lahan_psr_online = mysqli_query($conn_psr_online, $query_get_data_legalitas_lahan_psr_online);

            $array_hasil_get_data_legalitas_lahan_psr_online = mysqli_fetch_all($hasil_get_data_legalitas_lahan_psr_online, MYSQLI_ASSOC);

            foreach ($array_hasil_get_data_legalitas_lahan_psr_online as $identifier => $baris) {
                $query_get_polygon_psr_online = 'SELECT * FROM tb_kordinat WHERE id_legalitas = ' . $baris['id_legalitas'] . ' ORDER BY no_urut_kordinat ASC';
                $hasil_get_polygon_psr_online = mysqli_query($conn_psr_online, $query_get_polygon_psr_online);

                if ($hasil_get_polygon_psr_online->num_rows >= 1) {
                    $array_hasil_get_polygon_psr_online = mysqli_fetch_all($hasil_get_polygon_psr_online, MYSQLI_ASSOC);
                    $polygon_peta = "[";
                    foreach ($array_hasil_get_polygon_psr_online as $kunci => $nilai) {
                        $polygon_peta .= "[" . '"' . $nilai['longitude'] . '","' . $nilai['latitude'] . '"]';
                        if (isset($array_hasil_get_polygon_psr_online[$kunci + 1]) == true) {
                            $polygon_peta .= ",";
                        }
                    }
                    $polygon_peta .= "]";
                    $polygon_peta = str_replace("'", "\'", $polygon_peta);
                } else {
                    $polygon_peta = null;
                }

                @$baris['no_shm'] = str_replace("'", "\'", $baris['no_shm']);
                @$baris['no_shm'] = str_replace(",", "\,", $baris['no_shm']);
                @$baris['no_shm'] = str_replace('"', '\"', $baris['no_shm']);

                @$baris['no_skt'] = str_replace("'", "\'", $baris['no_skt']);
                @$baris['no_skt'] = str_replace(",", "\,", $baris['no_skt']);
                @$baris['no_skt'] = str_replace('"', '\"', $baris['no_skt']);

                @$baris['nama_shm'] = str_replace("'", "\'", $baris['nama_shm']);
                @$baris['nama_shm'] = str_replace(",", "\,", $baris['nama_shm']);
                @$baris['nama_shm'] = str_replace('"', '\"', $baris['nama_shm']);

                @$baris['nama_skt'] = str_replace("'", "\'", $baris['nama_skt']);
                @$baris['nama_skt'] = str_replace(",", "\,", $baris['nama_skt']);
                @$baris['nama_skt'] = str_replace('"', '\"', $baris['nama_skt']);

                $query_insert_legalitas_lahan_pekebun = 'insert into `legalitas_lahan_pekebun` (
                                                `id_proposal`,
                                                `id_pekebun`,
                                                `jenis_dokumen_legalitas_lahan`,
                                                `nomor_dokumen_legalitas_lahan`,
                                                `nama_tertera_dokumen_legalitas_lahan`,
                                                `tanggal_terbit_dokumen_legalitas_lahan`,
                                                `nama_asli_file_dokumen_legalitas_lahan`,
                                                `nama_file_dokumen_legalitas_lahan`,
                                                `lokasi_file_dokumen_legalitas_lahan`,
                                                `polygon_legalitas_lahan`,
                                                `luas_polygon_legalitas_lahan`,
                                                `polygon_verifikasi_legalitas_lahan`,
                                                `luas_polygon_verifikasi_legalitas_lahan`,
                                                `verifikator_verifikasi_legalitas_lahan`,
                                                `keterangan_verifikasi_legalitas_lahan`,
                                                `created_at`,
                                                `created_by`,
                                                `updated_at`,
                                                `updated_by`,
                                                `deleted_at`,
                                                `deleted_by`
                                            ) values (
                                                "' . $id_proposal . '",
                                                "' . $id_pekebun . '",
                                                "' . $baris['legalitas'] . '",
                                                "' . @($baris['no_shm'] === null ? $baris['no_skt'] : $baris['no_shm']) . '",
                                                "' . @($baris['nama_shm'] === null ? $baris['nama_skt'] : $baris['nama_shm']) . '",
                                                "' . @($baris['tgl_shm'] === null ? $baris['tgl_skt'] : $baris['tgl_shm']) . '",
                                                "' . @($baris['scan_shm'] === null ? $baris['scan_skt'] : $baris['scan_shm']) . '",
                                                "' . @($baris['scan_shm'] === null ? $baris['scan_skt'] : $baris['scan_shm']) . '",
                                                "' . @($baris['scan_shm'] === null ? $baris['scan_skt'] : $baris['scan_shm']) . '",
                                                ' . "'" . $polygon_peta . "'" . ',
                                                "' . $baris['luas_hektar'] . '",
                                                NULL,
                                                NULL,
                                                NULL,
                                                NULL,
                                                "' . $now . '",
                                                "' . $user . '",
                                                "' . $now . '",
                                                "' . $user . '",
                                                NULL,
                                                NULL
                                            )';

                mysqli_query($conn_rdp, $query_insert_legalitas_lahan_pekebun);

                $id_legalitas_lahan_pekebun = mysqli_insert_id($conn_rdp);

                $query_insert_log_legalitas_lahan = 'insert into `legalitas_lahan_pekebun_log` (
                                                `id_legalitas_lahan_pekebun`,
                                                `id_proposal`,
                                                `log`,
                                                `created_at`,
                                                `created_by`
                                            ) values (
                                                "' . $id_legalitas_lahan_pekebun . '",
                                                "' . $id_proposal . '",
                                                "Data Legalitas Pekebun ' .$row['nama_pekebun'].' NIK '. $row['no_ktp'] . ' - ' . $row['no_dokumen']. ', berhasil di migrasi oleh ' . $user . ' melalui aplikasi migrasi RDP",
                                                "' . $now . '",
                                                "' . $user . '"
                                            )';

                mysqli_query($conn_rdp, $query_insert_log_legalitas_lahan);

                $id_log_legalitas_lahan = mysqli_insert_id($conn_rdp);

            }

            echo ($row['nama_pekebun'] . " di Nomor Proposal " . $row['no_dokumen'] . " Berhasil di Migrasikan ke Database PSR Online V2!!! \n");
            $berhasil++;

        }

    }
}

$waktu_akhir = date('Y-m-d H:i:s');

$waktu_mulai = new DateTime($waktu_awal);
$waktu_selesai = new DateTime($waktu_akhir);

$waktu_berjalan = $waktu_selesai->diff($waktu_mulai);

echo "Migrasi Data Dimulai Dari ".$waktu_awal.", Sampai ".$waktu_akhir."(".$waktu_berjalan->h." Jam ".$waktu_berjalan->i." Menit ".$waktu_berjalan->s." Detik)\n";
echo("Dari $total_data_pekebun, Data Yang Berhasil Rekon Sebanyak : $berhasil, dan Gagal Rekon Sebanyak : $gagal.");

$conn_smart_psr->close();
$conn_rdp->close();
$conn_psr_online->close();
