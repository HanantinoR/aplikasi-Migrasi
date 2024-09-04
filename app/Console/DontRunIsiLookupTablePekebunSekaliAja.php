<?php

$servername_smart_psr = "127.0.0.1";
$username_smart_psr = "root";
$password_smart_psr = "";
$dbname_smart_psr = "psr2";

$servername_psr_online = "127.0.0.1";
$username_psr_online = "root";
$password_psr_online = "";
$dbname_psr_online = "psr";

$servername_rdp = "127.0.0.1";
$username_rdp = "root";
$password_rdp = "";
$dbname_rdp = "rdp";

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


$query_get_pekebun_proposal_psr_online = 'SELECT id_pekebun,id_proposal,no_ktp FROM tb_pekebun ORDER BY id_proposal ASC';

$query_get_pekebun_proposal_smart_psr = 'SELECT id,id_proposal,nik FROM tbl_master_pekebun ORDER BY id_proposal ASC';

// $queryGetProposalMT940PenyaluranBPDP = 'SELECT catatan_transaksi FROM transaksi_bpdp WHERE no_rekening_bpdp = "32901409999303" AND debit IS NOT NULL AND kredit IS NULL AND flag_rekon IS NULL AND keterangan_transaksi = "MSC" AND catatan_transaksi LIKE "PRO%" AND flag_rekon IS NULL GROUP BY catatan_transaksi ORDER BY catatan_transaksi ASC;';

// // Simpen List Id Mana Nyang Rekon Bossque
// $list_id_transaksi = array();

// // Simpen Juga List ID Proposalnya Bossque
// $list_id_proposal = array();

// // Counter Karena Kita Make Foreach, Karena Kita Malesh
// $i = 0;

//Jalankan Query CekMT940PenyaluranBPDP sama Biar Gatokinnya Enak Jadi Engga Lemodh Query Group No Proposalnya Maszeh
$hasil_cek_get_pekebun_proposal_psr_online = mysqli_query($conn_psr_online, $query_get_pekebun_proposal_psr_online);
$hasil_cek_get_pekebun_proposal_smart_psr = mysqli_query($conn_smart_psr, $query_get_proposal_smart_psr);

$array_pekebun = array();
$list_proposal_data_lookup_lengkap = array();

foreach ($hasil_cek_get_pekebun_proposal_psr_online as $key => $row) {
    $array_pekebun[$row['id_proposal']] = array(
        "id_proposal" => $row['id_proposal'],
        "id_pekebun" => $row['id_pekebun'],
        "nik" => $row['no_ktp']
    );
}

// // Cek Dulu Ada Apa Engga Ntu Nomor Proposal di SMART-PSR
// var_dump($array_proposal);

echo ("Hasil Fetch Nomor Proposal Penyaluran \n");
foreach ($hasil_cek_get_pekebun_proposal_smart_psr as $row) {
    if (in_array($row['nomor_proposal'],$list_proposal_psr_online)) {
        echo "hehe \n";
        $array_proposal[$row['nomor_proposal']] += [
            "id_proposal_smart_psr" => $row['id'],
            "id_kelembagaan_pekebun_smart_psr" => $row['id_lembaga_pekebun']
        ];
    }else{
        echo "not hehe :( \n";
    }
}

foreach ($array_proposal as $row) {
    if (in_array($row['nomor_proposal'],$list_proposal_data_lookup_lengkap)) {
        echo "Sudah Diinput, Data Akan Dihapus Dari List Lookup \n";
        $array_proposal[$row['nomor_proposal']] += [
            "id_proposal_smart_psr" => $row['id'],
            "id_kelembagaan_pekebun_smart_psr" => $row['id_lembaga_pekebun']
        ];
    }else{
        echo "Data ".$row['nomor_proposal']." Akan Diinput ke Tabel Lookup \n";
    }
}

mysqli_free_result($hasil_cek_get_proposal_psr_online);
mysqli_free_result($hasil_cek_get_proposal_smart_psr);

foreach ($array_proposal as $key => $row) {
    // var_dump($row['nomor_proposal']);
    if (isset($row['id_proposal_smart_psr'])) {
        // // Cek Udah Masuk Tabel Lookup Belom Ntu Id_Proposalnya
        // $query_cek_udah_lengkap_belom_di_rdp = "SELECT id_proposal_smart_psr FROM lookup_proposal_kelembagaan_pekebun WHERE nomor_proposal = '".$row['nomor_proposal'].";";

        // $hasil_query_cek_udah_lengkap_belom_di_rdp = mysqli_query($conn, $query_cek_udah_lengkap_belom_di_rdp);

        // if ($hasilCekqueryCekTransaksiPenyaluran[0]['id_proposal_smart_psr'] == NULL) {
        //     echo "Kosong \n";
        // }
        $query_insert_lookup = "INSERT INTO `lookup_proposal_kelembagaan_pekebun`(`id`, `nomor_proposal`, `id_proposal_psr_online`, `id_proposal_smart_psr`, `id_kelembagaan_pekebun_psr_online`, `id_kelembagaan_pekebun_smart_psr`, `created_at`, `created_by`) VALUES (NULL,'".$row['nomor_proposal']."','".$row['id_proposal_psr_online']."','".$row['id_proposal_smart_psr']."','".$row['id_kelembagaan_pekebun_psr_online']."','".$row['id_kelembagaan_pekebun_smart_psr']."','".date('Y-m-d H:i:s')."','ARI KURNIAWAN - Shell Script');";
    }else{
        $query_insert_lookup = "INSERT INTO `lookup_proposal_kelembagaan_pekebun`(`id`, `nomor_proposal`, `id_proposal_psr_online`, `id_proposal_smart_psr`, `id_kelembagaan_pekebun_psr_online`, `id_kelembagaan_pekebun_smart_psr`, `created_at`, `created_by`) VALUES (NULL,'".$row['nomor_proposal']."','".$row['id_proposal_psr_online']."',NULL,'".$row['id_kelembagaan_pekebun_psr_online']."',NULL,'".date('Y-m-d H:i:s')."','ARI KURNIAWAN - Shell Script');";
    }

    if ($conn_rdp->query($query_insert_lookup) === TRUE) {
        echo "Berhasil Insert Record ke General Ledger Atas Penyaluran " . $value['catatan_transaksi_bpdp'] . "\n";
    } else {
        echo "Error: " . $query_insert_lookup . "<br>" . $conn_rdp->error;
        break;
    }
}



// // Karena Nominal Banyak Sama, Biar ID Gak Ke Dobel Yang Rekon, Kita Simpen di String Maseh
// $string_id_transaksi_penyaluran = "0";
// // Baru Kita Cek Transaksinya
// foreach ($hasilCekMT940PenyaluranBPDP as $row) {
//     // Kalo Nomor Proposalnya Belom Diinput Sekip Aja Maseh
//     if($list_id_proposal[$row['catatan_transaksi']] == "Engga Ada"){
//         continue;
//     }else{
//         $queryCekTransaksiPenyaluran = 'SELECT * FROM transaksi WHERE kredit = ' . $row['debit'] . ' AND date(tanggal_transaksi) = "' . $row['tanggal_transaksi'] . '" AND id_proposal = ' . $list_id_proposal[$row['catatan_transaksi']] . ' AND flag_rekon IS NULL AND id NOT IN ('.$string_id_transaksi_penyaluran.') ORDER BY tanggal_transaksi ASC LIMIT 1;';
//         $hasilCekqueryCekTransaksiPenyaluran = mysqli_query($conn, $queryCekTransaksiPenyaluran);

//         if ($hasilCekqueryCekTransaksiPenyaluran->num_rows === 0) {
//             echo "Kosong \n";
//         } else {
//             echo "Ditemukan Transaksi Penyaluran di Rekening LP! \n";
//             print_r($hasilCekqueryCekTransaksiPenyaluran);
//             foreach ($hasilCekqueryCekTransaksiPenyaluran as $hasil) {
//                 print_r($hasil);
//                 $list_id_transaksi[$i]['id_transaksi_bpdp'] = $row['id'];
//                 $list_id_transaksi[$i]['keterangan_transaksi_bpdp'] = $row['keterangan_transaksi'];
//                 $list_id_transaksi[$i]['catatan_transaksi_bpdp'] = $row['catatan_transaksi'];
//                 $list_id_transaksi[$i]['id_bank'] = $row['id_bank'];
//                 $list_id_transaksi[$i]['no_rekening_bpdp'] = $row['no_rekening_bpdp'];
//                 $list_id_transaksi[$i]['id_transaksi'] = $hasil['id'];
//                 $list_id_transaksi[$i]['id_proposal'] = $hasil['id_proposal'];
//                 $list_id_transaksi[$i]['tanggal_transaksi'] = $hasil['tanggal_transaksi'];
//                 $list_id_transaksi[$i]['id_lembaga_pekebun'] = $hasil['id_lembaga_pekebun'];
//                 $list_id_transaksi[$i]['nominal'] = $hasil['nilai_transaksi'];
//                 $string_id_transaksi_penyaluran .= ",".$hasil['id'];
//             }
//         }
//     }


//     mysqli_free_result($hasilCekqueryCekTransaksiPenyaluran);
//     $i++;
// }

// mysqli_free_result($hasilCekMT940PenyaluranBPDP);
// // // Reset Keys Array Nya Karena Kita Make Foreach, Jadi Bolong Bolong
// $list_id_transaksi = array_values($list_id_transaksi);

// print_r($list_id_transaksi);
// // // Reset Angka Iterasinya
// $i = 0;


// foreach ($list_id_transaksi as $key => $value) {
//     //Masukkeun ke GL dulu
//     $queryInsertIntoGeneralLedger = 'INSERT INTO general_ledger VALUES (NULL, ' . $value['id_proposal'] . ', ' . $value['id_lembaga_pekebun'] . ', ' . $value['nominal'] . ', NULL, ' . $value['nominal'] . ', NULL, "' . $value['tanggal_transaksi'] . '", "DANA PSR", NULL, NULL, "' . date('Y-m-d H:i:s') . '", "' . date('Y-m-d H:i:s') . '", "Auto Rekon SMART-PSR_Penyaluran", ' . $value['id_transaksi'] . ', NULL, NULL, NULL, 0)';

//     if ($conn->query($queryInsertIntoGeneralLedger) === TRUE) {
//         echo "Berhasil Insert Record ke General Ledger Atas Penyaluran " . $value['catatan_transaksi_bpdp'] . "\n";
//     } else {
//         echo "Error: " . $queryInsertIntoGeneralLedger . "<br>" . $conn->error;
//         break;
//     }

//     //Ambil ID GL Nya, terus Simpen ke Array $value nya
//     $queryLastidGL = 'SELECT * FROM general_ledger WHERE id_proposal = "' . $value['id_proposal'] . '" ORDER BY id DESC LIMIT 1;';
//     $getLastidGL = mysqli_query($conn, $queryLastidGL);
//     $lastidGL = mysqli_fetch_row($getLastidGL);
//     $value['id_general_ledger'] = $lastidGL[0];
//     mysqli_free_result($getLastidGL);

//     // Update Transaksinya Biar Gak Muncul Lagi
//     $updateTrxPenyaluran = 'UPDATE transaksi SET id_general_ledger = "' . $value['id_general_ledger'] . '", flag_rekon = "1", updated_at = "' . date('Y-m-d H:i:s') . '", updated_by = "Auto Rekon SMART-PSR_Penyaluran" WHERE id = "' . $value['id_transaksi'] . '";';
//     if ($conn->query($updateTrxPenyaluran) === TRUE) {
//         echo "Berhasil Update Transaksi, updating Salah Satu Transaksi Nomor Proposal " . $value['catatan_transaksi_bpdp'] . " Menjadi Sudah Di Rekon \n";
//     } else {
//         echo "Error: " . $updateTrxPenyaluran . "<br>" . $conn->error;
//         break;
//     }

//     // Update Tabel General Ledger BPDP
//     $queryInsertIntoGeneralLedger = 'INSERT INTO general_ledger_bpdp VALUES (NULL, ' . $value['id_bank'] . ', ' . $value['no_rekening_bpdp'] . ', ' . $value['nominal'] . ', ' . $value['nominal'] . ', NULL, NULL, "' . $value['tanggal_transaksi'] . '", "DANA PSR", "' . $value['catatan_transaksi_bpdp'] . '", NULL, "' . date('Y-m-d H:i:s') . '", "' . date('Y-m-d H:i:s') . '", "Auto Rekon SMART-PSR_Penyaluran", ' . $value['id_transaksi_bpdp'] . ', NULL, NULL, NULL)';

//     if ($conn->query($queryInsertIntoGeneralLedger) === TRUE) {
//         echo "Berhasil Insert Record ke General Ledger BPDP Atas Penyaluran Nomor Proposal " . $value['catatan_transaksi_bpdp'] . "\n";
//     } else {
//         echo "Error: " . $queryInsertIntoGeneralLedger . "<br>" . $conn->error;
//         break;
//     }

//     // Ambil ID GL BPDP Nya
//     $queryLastidGLBPDP = 'SELECT * FROM general_ledger_bpdp WHERE no_rekening_bpdp = "' . $value['no_rekening_bpdp'] . '" ORDER BY id DESC LIMIT 1;';
//     $getLastidGLBPDP = mysqli_query($conn, $queryLastidGLBPDP);
//     $lastidGLBPDP = mysqli_fetch_row($getLastidGLBPDP);
//     $value['id_general_ledger_bpdp'] = $lastidGLBPDP[0];
//     mysqli_free_result($getLastidGLBPDP);

//     // Update Tabel Transaksi BPDP
//     $updateTrxPenyaluranBPDP = 'UPDATE transaksi_bpdp SET id_general_ledger = ' . $value['id_general_ledger'] . ', id_general_ledger_bpdp = ' . $value['id_general_ledger_bpdp'] . ', id_transaksi = ' . $value['id_transaksi'] . ', flag_rekon = "1", updated_at = "' . date('Y-m-d H:i:s') . '", updated_by = "Auto Rekon SMART-PSR_Penyaluran" WHERE id = "' . $value['id_transaksi_bpdp'] . '";';
//     if ($conn->query($updateTrxPenyaluranBPDP) === TRUE) {
//         echo "Berhasil Update Transaksi BPDP, updating Penyaluran Nomor Proposal  " . $value['catatan_transaksi_bpdp'] . " Menjadi Sudah Di Rekon \n";
//     } else {
//         echo "Error: " . $updateTrxPenyaluranBPDP . "<br>" . $conn->error;
//         break;
//     }

//     // Masukkin Kejadian Ke Log Rekon Pengembalian Biar Bisa di Gatokin di Excel
//     $queryInsertLogRekonPenyaluran = 'INSERT INTO log_rekon_penyaluran VALUES (NULL, ' . $value['id_transaksi'] . ', ' . $value['id_transaksi_bpdp'] . ', ' . $value['id_general_ledger'] . ', ' . $value['id_general_ledger_bpdp'] . ', "Berhasil Rekonsiliasi Salah Satu Penyaluran Nomor Proposal' . $value['catatan_transaksi_bpdp'] . ' dengan Uang Keluar di Nomor Rekening BPDP ' . $value['no_rekening_bpdp'] . '", "' . date('Y-m-d H:i:s') . '", "Auto Rekon SMART-PSR_Penyaluran")';
//     if ($conn->query($queryInsertLogRekonPenyaluran) === TRUE) {
//         echo "Berhasil Insert Log Rekon Pengembalian \n";
//     } else {
//         echo "Error: " . $queryInsertLogRekonPenyaluran . "<br>" . $conn->error;
//         break;
//     }

//     $i++;
// }
// // Masukkin Kejadian Ke MT940 Sync Biar Ke-data ajah
// if ($i > 0) {
//     $queryInsertLogMT940Sync = 'INSERT INTO mt940sync VALUES (NULL, ' . $i . ', "' . date('Y-m-d H:i:s') . '", "Auto Rekon_Penyaluran")';
//     if ($conn->query($queryInsertLogMT940Sync) === TRUE) {
//         echo "Berhasil Rekon $i Transaksi Penyaluran \n";
//     } else {
//         echo "Error: " . $queryInsertLogMT940Sync . "<br>" . $conn->error;
//     }
// }



$conn_smart_psr->close();
$conn_rdp->close();
$conn_psr_online->close();

