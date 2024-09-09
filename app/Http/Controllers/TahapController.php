<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use DB;

class TahapController extends Controller
{
    public function index1()
    {
        $get_id_proposal = DB::connection('mysql_rdp')
                            ->table('lookup_proposal_kelembagaan_pekebun')
                            ->get();

        $list_id_proposal_psr_online = $get_id_proposal->pluck('id_proposal_psr_online');

        $get_data_kelembagaan_pekebun = DB::connection('mysql_psr')
                                        ->table('tb_proposal')
                                        ->whereIn('tb_proposal.id_proposal',$list_id_proposal_psr_online)
                                        ->join('tb_koperasi','tb_koperasi.id_koperasi','=','tb_proposal.id_koperasi')
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi','tb_proposal.sk_penetapan_dirut')
                                        ->orderBy('tb_proposal.id_proposal','ASC')
                                        ->whereNotNull('no_sk_dirut')
                                        ->get();

        return view('tahap1.index')->with(compact('get_data_kelembagaan_pekebun'));
    }

    public function detail1(Request $request, $id_proposal)
    {
        $get_id_proposal_smart_psr = DB::connection('mysql_rdp')
                            ->table('lookup_proposal_kelembagaan_pekebun')
                            ->where('id_proposal_psr_online','=',$id_proposal)
                            ->value('id_proposal_smart_psr');

        $data_pekebun_psr_online = DB::connection('mysql_psr')
                                ->table('tb_pekebun')
                                ->where('id_proposal','=',$id_proposal)
                                ->orderBy('nama_pekebun','ASC')
                                ->get();

        $data_pekebun_smart_psr = DB::connection('mysql_smart_psr')
                                ->table('tbl_master_pekebun')
                                ->where('id_proposal','=',$get_id_proposal_smart_psr)
                                ->orderBy('nama_pekebun','ASC')
                                ->get();

        $get_data_kelembagaan_pekebun = DB::connection('mysql_psr')
                                        ->table('tb_proposal')
                                        ->where('tb_proposal.id_proposal','=',$id_proposal)
                                        ->join('tb_koperasi','tb_koperasi.id_koperasi','=','tb_proposal.id_koperasi')
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi','sk_penetapan_dirut')
                                        ->orderBy('tb_proposal.id_proposal','ASC')
                                        ->first();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
                                ->table('lookup_pekebun_proposal')
                                ->where('id_proposal_psr_online','=',$id_proposal)
                                ->where('id_proposal_smart_psr','=',$get_id_proposal_smart_psr)
                                ->get();

        $list_id_pekebun_sudah_rekon = $get_id_hasil_rekon->pluck('id_pekebun_rdp');

        $id_proposal_rdp = $get_id_hasil_rekon->value('id_proposal_rdp');

        $data_pekebun_sudah_rekon = DB::connection('mysql_rdp')
                            ->table('pekebun')
                            ->whereIn('id',$list_id_pekebun_sudah_rekon)
                            ->orderBy('nama_pekebun','ASC')
                            ->get();

        $data_pekebun_sudah_rekon = $data_pekebun_sudah_rekon->keyBy('id');

        foreach ($data_pekebun_sudah_rekon as $key => $value) {
            $data_pekebun_sudah_rekon[$key]->luas_lahan = 0.0000;
        }

        $get_data_legalitas = DB::connection('mysql_rdp')
                              ->table('legalitas_lahan_pekebun')
                              ->whereIn('id_pekebun',$list_id_pekebun_sudah_rekon)
                              ->where('id_proposal','=',$id_proposal_rdp)
                              ->get();

        foreach ($get_data_legalitas as $key => $value) {
            $data_pekebun_sudah_rekon[$value->id_pekebun]->luas_lahan += $value->luas_polygon_legalitas_lahan;
        }

        // Key By Data Pekebun buat di Apus Kalo Dia Udah di Rekon
        $data_pekebun_psr_online = $data_pekebun_psr_online->keyBy('id_pekebun');
        $data_pekebun_smart_psr = $data_pekebun_smart_psr->keyBy('id');
        // dd($get_id_hasil_rekon);
        foreach ($get_id_hasil_rekon as $key => $value) {
            unset($data_pekebun_psr_online[$value->id_pekebun_psr_online]);
            unset($data_pekebun_smart_psr[$value->id_pekebun_smart_psr]);
        }

        return view('tahap1.detail')->with(compact('data_pekebun_psr_online','data_pekebun_smart_psr','get_data_kelembagaan_pekebun','data_pekebun_sudah_rekon'));
    }

    public function post_rekon_tahap_1(Request $request) {
        $now = date('Y-m-d H:i:s');
        $get_data_pekebun_psr_online =  DB::connection('mysql_psr')
                                        ->table('tb_pekebun')
                                        ->where('id_pekebun','=',$request->id_pekebun_psr_online)
                                        ->first();

        $get_data_pekebun_smart_psr =   DB::connection('mysql_smart_psr')
                                        ->table('tbl_master_pekebun')
                                        ->where('id','=',$request->id_pekebun_smart_psr)
                                        ->first();

        $nomor_proposal =   DB::connection('mysql_psr')
                            ->table('tb_proposal')
                            ->where('id_proposal','=',$get_data_pekebun_psr_online->id_proposal)
                            ->value('no_dokumen');

        $id_koperasi_psr_online =   DB::connection('mysql_psr')
                                    ->table('tb_proposal')
                                    ->where('id_proposal','=',$get_data_pekebun_psr_online->id_proposal)
                                    ->value('id_koperasi');

        $get_data_kelembagaan_pekebun_psr_online = DB::connection('mysql_psr')
                                    ->table('tb_koperasi')
                                    ->where('id_koperasi','=',$id_koperasi_psr_online)
                                    ->first();

        $get_data_proposal = DB::connection('mysql_psr')
                             ->table('tb_proposal')
                             ->where('id_proposal','=',$get_data_pekebun_psr_online->id_proposal)
                             ->first();

        $cek_nik_udah_kedaftar_apa_belom =  DB::connection('mysql_rdp')
                                            ->table('pekebun')
                                            ->where('nik_pekebun','=',trim($get_data_pekebun_psr_online->no_ktp))
                                            ->first();

        // Cek Udah Ke Daftar Belom Proposal Sama LP Nya
        $cek_terdaftar_lembaga_pekebun =    DB::connection('mysql_rdp')
                                            ->table('kelembagaan_pekebun')
                                            ->where('nama_kelembagaan_pekebun','=',trim($get_data_kelembagaan_pekebun_psr_online->koperasi))
                                            ->first();

        $cek_proposal_rdp =  DB::connection('mysql_rdp')
                                            ->table('legalitas_lahan_pekebun')
                                            ->value('polygon_legalitas_lahan');

        if ($cek_terdaftar_lembaga_pekebun === null) {
            $id_kelembagaan_pekebun = DB::connection('mysql_rdp')
                                      ->table('kelembagaan_pekebun')
                                      ->insertGetId([
                                        "nama_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->koperasi,
                                        "provinsi_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->provinsi,
                                        "kota_kabupaten_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->kabupaten,
                                        "kecamatan_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->kecamatan,
                                        "kelurahan_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->kelurahan,
                                        "kodepos_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->kodepos,
                                        "alamat_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->alamat,
                                        "nomor_telepon_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->tlp,
                                        "nomor_hp_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->hp,
                                        "email_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->email,
                                        "jenis_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->jenis_kelembagaan,
                                        "file_dokumen_legalitas_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->legalitas,
                                        "nama_dokumen_legalitas_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->legalitas,
                                        "nomor_dokumen_legalitas_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->no_legalitas_koperasi,
                                        "instansi_pengesahan_dokumen_legalitas_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->notaris,
                                        "tanggal_terbit_dokumen_legalitas_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->tgl_terbit_legalitas,
                                        "titik_koordinat_kantor_kelembagaan_pekebun" => NULL,
                                        "logo_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->foto,
                                        "nama_ketua_kelembagaan_pekebun" => $get_data_kelembagaan_pekebun_psr_online->pimpinan,
                                        "jenis_kelamin_ketua_kelembagaan_pekebun" => NULL,
                                        "tempat_lahir_ketua_kelembagaan_pekebun" => NULL,
                                        "tanggal_lahir_ketua_kelembagaan_pekebun" => NULL,
                                        "nik_ketua_kelembagaan_pekebun" => NULL,
                                        "kk_ketua_kelembagaan_pekebun" => NULL,
                                        "status_pernikahan_ketua_kelembagaan_pekebun" => NULL,
                                        "nomor_telepon_ketua_kelembagaan_pekebun" => NULL,
                                        "nomor_hp_ketua_kelembagaan_pekebun" => NULL,
                                        "provinsi_ketua_kelembagaan_pekebun" => NULL,
                                        "kota_kabupaten_ketua_kelembagaan_pekebun" => NULL,
                                        "kecamatan_ketua_kelembagaan_pekebun" => NULL,
                                        "kelurahan_ketua_kelembagaan_pekebun" => NULL,
                                        "kodepos_ketua_kelembagaan_pekebun" => NULL,
                                        "alamat_ketua_kelembagaan_pekebun" => NULL,
                                        "foto_ketua_kelembagaan_pekebun" => NULL,
                                        "foto_ktp_ketua_kelembagaan_pekebun" => NULL,
                                        "file_dokumen_penunjukkan_ketua_kelembagaan_pekebun" => NULL,
                                        "nama_dokumen_penunjukkan_ketua_kelembagaan_pekebun" => NULL,
                                        "nomor_dokumen_penunjukkan_ketua_kelembagaan_pekebun" => NULL,
                                        "tanggal_terbit_dokumen_penunjukkan_ketua_kelembagaan_pekebun" => NULL,
                                        "nama_pic_kelembagaan_pekebun" => NULL,
                                        "nomor_pic_kelembagaan_pekebun" => NULL,
                                        "created_at" => $now,
                                        "created_by" => Auth::user()->name,
                                        "updated_at" => $now,
                                        "updated_by" => Auth::user()->name,
                                        "deleted_at" => NULL,
                                        "foto_kk_ketua_kelembagaan_pekebun" => NULL,
                                      ]);
        }else{
            $id_kelembagaan_pekebun = $cek_terdaftar_lembaga_pekebun->id;
        }

        $cek_proposal_rdp =  DB::connection('mysql_rdp')
                                            ->table('proposal')
                                            ->where('nomor_proposal','=',trim($nomor_proposal))
                                            ->first();

        if ($cek_proposal_rdp === null) {
            // dd($get_data_proposal);
            $id_nomor_proposal = DB::connection('mysql_rdp')
                                 ->table('proposal')
                                 ->insertGetId([
                                    "id_kelembagaan_pekebun" => $id_kelembagaan_pekebun,
                                    "nomor_proposal" => $nomor_proposal,
                                    "tanggal_pengajuan_proposal" => date('Y-m-d',strtotime($get_data_proposal->tgl_input)),
                                    "jalur_pengajuan" => $get_data_proposal->jalur,
                                    "status_lahan" => NULL,
                                    "bank_mitra" => $get_data_proposal->nama_bank,
                                    "cabang_bank_mitra" => $get_data_proposal->cabang_bank_proposals,
                                    "rencana_anggaran_belanja" => NULL,
                                    "produktifitas_tanaman" => $get_data_proposal->produktivitas_tanaman == null ? 0.0000 : $get_data_proposal->produktivitas_tanaman,
                                    "tahun_tanam_tanaman" => $get_data_proposal->tahun_tanaman,
                                    "luas_lahan_diajukan" => NULL,
                                    "luas_lahan_didanai" => NULL,
                                    "luas_lahan_dikembalikan" => NULL,
                                    "status_verifikasi_dokumen" => NULL,
                                    "tanggal_verifikasi_dokumen" => NULL,
                                    "verifikator_verifikasi_dokumen" => NULL,
                                    "keterangan_verifikasi_dokumen" => NULL,
                                    "created_at" => $now,
                                    "created_by" => Auth::user()->name,
                                    "updated_at" => $now,
                                    "updated_by" => Auth::user()->name,
                                    "step" => NULL,
                                    "status_push_verifikator" => NULL,
                                 ]);
        }else{
            $id_nomor_proposal = $cek_proposal_rdp->id;
        }



        // dd($nomor_proposal,$request->all(),$cek_nik_udah_kedaftar_apa_belom,$get_data_pekebun_psr_online->tgl_lahir);

        if ($cek_nik_udah_kedaftar_apa_belom === null) {
            // Insert ke Table pekebun
            $id_pekebun = DB::connection('mysql_rdp')
            ->table('pekebun')
            ->insertGetId([
                "nik_pekebun" => trim($get_data_pekebun_psr_online->no_ktp) == null ? NULL : trim($get_data_pekebun_psr_online->no_ktp),
                "kk_pekebun" => trim($get_data_pekebun_psr_online->no_kk) == null ? NULL : trim($get_data_pekebun_psr_online->no_kk),
                "nama_pekebun" => strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->nama_pekebun)),
                "tanggal_lahir_pekebun" => trim($get_data_pekebun_psr_online->tgl_lahir) == null ? NULL : trim($get_data_pekebun_psr_online->tgl_lahir),
                "jenis_kelamin_pekebun" => trim($get_data_pekebun_psr_online->jenis_kelamin) == null ? NULL : trim($get_data_pekebun_psr_online->jenis_kelamin),
                "status_pernikahan_pekebun" => trim($get_data_pekebun_psr_online->status_pernikahan) == null ? NULL : trim($get_data_pekebun_psr_online->status_pernikahan),
                "nomor_hp_pekebun" => trim($get_data_pekebun_psr_online->hp_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->hp_pekebun),
                "provinsi_pekebun" => trim($get_data_pekebun_psr_online->provinsi) == null ? NULL : trim($get_data_pekebun_psr_online->provinsi),
                "kota_kabupaten_pekebun" => trim($get_data_pekebun_psr_online->kabupaten) == null ? NULL : trim($get_data_pekebun_psr_online->kabupaten),
                "kecamatan_pekebun" => trim($get_data_pekebun_psr_online->kecamatan) == null ? NULL : trim($get_data_pekebun_psr_online->kecamatan),
                "kelurahan_pekebun" => trim($get_data_pekebun_psr_online->kelurahan) == null ? NULL : trim($get_data_pekebun_psr_online->kelurahan),
                "kodepos_pekebun" => trim($get_data_pekebun_psr_online->kodepos) == null ? NULL : trim($get_data_pekebun_psr_online->kodepos),
                "alamat_pekebun" => trim($get_data_pekebun_psr_online->alamat_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->alamat_pekebun),
                "luas_lahan_tersedia" => 4.0000,
                "created_at" => $now,
                "created_by" => Auth::user()->name,
                "updated_at" => $now,
                "updated_by" => Auth::user()->name
            ]);
        }else{
            $id_pekebun = $cek_nik_udah_kedaftar_apa_belom->id;
        }
            // Insert ke Table lookup pekebun proposal
            DB::connection('mysql_rdp')
            ->table('lookup_pekebun_proposal')
            ->insert([
                'nomor_proposal' => $nomor_proposal,
                'id_proposal_psr_online' => $get_data_pekebun_psr_online->id_proposal,
                'id_proposal_smart_psr' => $get_data_pekebun_smart_psr->id_proposal,
                'id_pekebun_psr_online' => $get_data_pekebun_psr_online->id_pekebun,
                'id_pekebun_smart_psr' => $get_data_pekebun_smart_psr->id,
                'created_at' => $now,
                'created_by' => Auth::user()->name,
                'id_proposal_rdp' => $id_nomor_proposal,
                'id_kelembagaan_pekebun_rdp' => $id_kelembagaan_pekebun,
                'id_pekebun_rdp' => $id_pekebun
            ]);

            // Insert ke Table lookup Pekebunnya Proposal
            DB::connection('mysql_rdp')
            ->table('pekebunnya_proposal')
            ->insert([
                'id_kelembagaan_pekebun' => $id_kelembagaan_pekebun,
                'id_proposal' => $id_nomor_proposal,
                'id_pekebun' => $id_pekebun,
                'created_at' => $now,
                'created_by' => Auth::user()->name
            ]);

            // Insert ke Table lookup Pekebunnya Kelembagaan
            DB::connection('mysql_rdp')
            ->table('pekebunnya_kelembagaan')
            ->insert([
                'id_kelembagaan_pekebun' => $id_kelembagaan_pekebun,
                'id_pekebun' => $id_pekebun,
                'created_at' => $now,
                'created_by' => Auth::user()->name
            ]);

            // Insert Log Pekebun Di Migrasiin
            DB::connection('mysql_rdp')
            ->table('pekebun_log')
            ->insert([
                'id_pekebun' => $id_pekebun,
                'log' => "Pekebun ".strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->nama_pekebun))." (NIK: ".trim($get_data_pekebun_psr_online->no_ktp) == null ? NULL : trim($get_data_pekebun_psr_online->no_ktp).") di migrasi oleh User RDP - ".Auth::user()->email." melalui aplikasi migrasi RDP",
                'created_at' => $now,
                'created_by' => "RDP - ".Auth::user()->name
            ]);

            // Insert Legalitas Dia
            $get_data_legalitas = DB::connection('mysql_psr')
                                  ->table('tb_legalitas')
                                  ->where('id_pekebun','=',$request->id_pekebun_psr_online)
                                  ->orderBy('id_legalitas','ASC')
                                  ->get();
            foreach ($get_data_legalitas as $key => $value) {
                $polygon_psr_online = DB::connection('mysql_psr')
                                      ->table('tb_kordinat')
                                      ->where('id_legalitas','=',$value->id_legalitas)
                                      ->orderBy('no_urut_kordinat','ASC')
                                      ->get();
                if (count($polygon_psr_online) >= 1) {
                    $polygon_peta = "[";
                    foreach ($polygon_psr_online as $key => $tikor) {
                        $polygon_peta .= "[".'"'.$tikor->longitude.'","'.$tikor->latitude.'"]';
                        if (isset($polygon_psr_online[$key+1]) == true) {
                            $polygon_peta .= ",";
                        }
                    }
                    $polygon_peta .= "]";
                }else{
                    $polygon_peta = NULL;
                }
                $id_legalitas = DB::connection('mysql_rdp')
                                ->table('legalitas_lahan_pekebun')
                                ->insert([
                                    "id_proposal" => $id_nomor_proposal,
                                    "id_pekebun" => $id_pekebun,
                                    "jenis_dokumen_legalitas_lahan" => $value->legalitas,
                                    "nomor_dokumen_legalitas_lahan" => $value->no_shm === null ? $value->no_skt : $value->no_shm,
                                    "nama_tertera_dokumen_legalitas_lahan" => $value->nama_shm === null ? $value->nama_skt : $value->nama_shm,
                                    "tanggal_terbit_dokumen_legalitas_lahan" => $value->tgl_shm === null ? $value->tgl_skt : $value->tgl_shm,
                                    "nama_asli_file_dokumen_legalitas_lahan" => $value->scan_shm === null ? $value->scan_skt : $value->scan_shm,
                                    "nama_file_dokumen_legalitas_lahan" => $value->scan_shm === null ? $value->scan_skt : $value->scan_shm,
                                    "lokasi_file_dokumen_legalitas_lahan" => $value->scan_shm === null ? $value->scan_skt : $value->scan_shm,
                                    "polygon_legalitas_lahan" => $polygon_peta,
                                    "luas_polygon_legalitas_lahan" => $value->luas_hektar,
                                    "polygon_verifikasi_legalitas_lahan" => NULL,
                                    "luas_polygon_verifikasi_legalitas_lahan" => NULL,
                                    "verifikator_verifikasi_legalitas_lahan" => NULL,
                                    "keterangan_verifikasi_legalitas_lahan" => NULL,
                                    "created_at" => $now,
                                    "created_by" => Auth::user()->name,
                                    "updated_at" => $now,
                                    "updated_by" => Auth::user()->name,
                                    "deleted_at" => NULL,
                                    "deleted_by" => NULL,
                                ]);

                // Insert Log Legalitas Pekebun Di Migrasiin
                DB::connection('mysql_rdp')
                ->table('legalitas_lahan_pekebun_log')
                ->insert([
                    'id_legalitas_lahan_pekebun' => $id_legalitas,
                    'id_proposal' => $id_nomor_proposal,
                    'log' => "Legalitas Pekebun ".strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->nama_pekebun))." (NIK: ".trim($get_data_pekebun_psr_online->no_ktp) == null ? NULL : trim($get_data_pekebun_psr_online->no_ktp).") di migrasi oleh User RDP - ".Auth::user()->email." melalui aplikasi migrasi RDP",
                    'created_at' => $now,
                    'created_by' => "RDP - ".Auth::user()->name
                ]);
            }

        return redirect()->back();

    }

    public function index2()
    {
        $get_data_kelembagaan_pekebun = DB::connection('mysql_rdp')
                            ->table('proposal')
                            ->join('kelembagaan_pekebun','proposal.id_kelembagaan_pekebun','=','kelembagaan_pekebun.id')
                            ->select('proposal.id','proposal.nomor_proposal','kelembagaan_pekebun.nama_kelembagaan_pekebun')
                            ->orderBy('proposal.id','ASC')
                            ->get();
        return view('tahap2.index')->with(compact('get_data_kelembagaan_pekebun'));
    }

    public function detail2($id_proposal)
    {
        $get_data_kelembagaan_pekebun = DB::connection('mysql_rdp')
        ->table('proposal')
        ->join('kelembagaan_pekebun','proposal.id_kelembagaan_pekebun','=','kelembagaan_pekebun.id')
        ->select('proposal.id','proposal.nomor_proposal','kelembagaan_pekebun.nama_kelembagaan_pekebun')
        ->where('proposal.id','=',$id_proposal)
        ->orderBy('proposal.id','ASC')
        ->first();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
        ->table('lookup_pekebun_proposal')
        ->where('id_proposal_rdp','=',$id_proposal)
        ->get();

    $list_id_pekebun_sudah_rekon = $get_id_hasil_rekon->pluck('id_pekebun_rdp');

        $data_pekebun_sudah_rekon = DB::connection('mysql_rdp')
                            ->table('pekebun')
                            ->whereIn('id',$list_id_pekebun_sudah_rekon)
                            ->orderBy('nama_pekebun','ASC')
                            ->get();

        $data_pekebun_sudah_rekon = $data_pekebun_sudah_rekon->keyBy('id');

        foreach ($data_pekebun_sudah_rekon as $key => $value) {
            $data_pekebun_sudah_rekon[$key]->luas_lahan = 0.0000;
        }

        $get_data_legalitas = DB::connection('mysql_rdp')
                              ->table('legalitas_lahan_pekebun')
                              ->whereIn('id_pekebun',$list_id_pekebun_sudah_rekon)
                              ->where('id_proposal','=',$id_proposal)
                              ->get();

        foreach ($get_data_legalitas as $key => $value) {
            $data_pekebun_sudah_rekon[$value->id_pekebun]->luas_lahan += $value->luas_polygon_legalitas_lahan;
        }


        return view('tahap2.detail')->with(compact('get_data_kelembagaan_pekebun','data_pekebun_sudah_rekon'));
    }

    public function dokumen_pekebun_2($id_pekebun,$id_proposal)
    {
        $get_data_pekebun = DB::connection('mysql_rdp')
        ->table('pekebun')
        ->join('pekebunnya_kelembagaan','pekebunnya_kelembagaan.id_pekebun','=','pekebun.id')
        ->join('kelembagaan_pekebun','kelembagaan_pekebun.id','=','pekebunnya_kelembagaan.id_kelembagaan_pekebun')
        ->select('pekebun.*')
        ->where('pekebun.id','=',$id_pekebun)
        ->first();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
        ->table('lookup_pekebun_proposal')
        ->where('id_proposal_rdp','=',$id_proposal)
        ->where('id_pekebun_rdp','=',$id_pekebun)
        ->first();

        $get_dokumen_psr_online =   DB::connection('mysql_psr')
        ->table('tb_pekebun')
        ->where('id_pekebun','=',$get_id_hasil_rekon->id_pekebun_psr_online)
        ->where('id_proposal','=',$get_id_hasil_rekon->id_proposal_psr_online)
        ->first();

        return view('tahap2.dokumen_pekebun')->with(compact('get_data_pekebun','get_dokumen_psr_online'));
    }

    public function post_dokumen_pekebun_2()
    {
        dd($_POST);
        $get_data_pekebun = DB::connection('mysql_rdp')
        ->table('pekebun')
        ->join('pekebunnya_kelembagaan','pekebunnya_kelembagaan.id_pekebun','=','pekebun.id')
        ->join('kelembagaan_pekebun','kelembagaan_pekebun.id','=','pekebunnya_kelembagaan.id_kelembagaan_pekebun')
        ->select('pekebun.*')
        ->where('pekebun.id','=',$id_pekebun)
        ->first();

        return view('tahap2.dokumen_pekebun')->with(compact('get_data_pekebun'));
    }

    public function index3()
    {
        $get_id_proposal = DB::connection('mysql_rdp')
                            ->table('lookup_proposal_kelembagaan_pekebun')
                            ->get();

        $list_id_proposal_psr_online = $get_id_proposal->pluck('id_proposal_psr_online');

        $get_data_kelembagaan_pekebun = DB::connection('mysql_psr')
                                        ->table('tb_proposal')
                                        ->whereIn('tb_proposal.id_proposal',$list_id_proposal_psr_online)
                                        ->join('tb_koperasi','tb_koperasi.id_koperasi','=','tb_proposal.id_koperasi')
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi','tb_proposal.sk_penetapan_dirut')
                                        ->orderBy('tb_proposal.id_proposal','ASC')
                                        ->whereNotNull('no_sk_dirut')
                                        ->get();

        return view('tahap3.index')->with(compact('get_data_kelembagaan_pekebun'));
    }

    public function detail3()
    {
        return view('tahap3.detail');
    }

    public function index4()
    {
        $get_data_kelembagaan_pekebun = DB::connection('mysql_rdp')
                            ->table('proposal')
                            ->join('kelembagaan_pekebun','proposal.id_kelembagaan_pekebun','=','kelembagaan_pekebun.id')
                            ->select('proposal.id','proposal.nomor_proposal','kelembagaan_pekebun.nama_kelembagaan_pekebun')
                            ->orderBy('proposal.id','ASC')
                            ->get();

        return view('tahap4.index')->with(compact('get_data_kelembagaan_pekebun'));
    }

    public function detail4($id_proposal)
    {
        $get_data_kelembagaan_pekebun = DB::connection('mysql_rdp')
        ->table('proposal')
        ->join('kelembagaan_pekebun','proposal.id_kelembagaan_pekebun','=','kelembagaan_pekebun.id')
        ->select('proposal.id','proposal.nomor_proposal','kelembagaan_pekebun.nama_kelembagaan_pekebun','kelembagaan_pekebun.id AS id_kelembagaan_pekebun')
        ->where('proposal.id','=',$id_proposal)
        ->orderBy('proposal.id','ASC')
        ->first();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
        ->table('lookup_pekebun_proposal')
        ->where('id_proposal_rdp','=',$id_proposal)
        ->get();

        $list_id_pekebun_sudah_rekon = $get_id_hasil_rekon->pluck('id_pekebun_rdp');

        $data_pekebun_sudah_rekon = DB::connection('mysql_rdp')
                            ->table('pekebun')
                            ->whereIn('id',$list_id_pekebun_sudah_rekon)
                            ->orderBy('nama_pekebun','ASC')
                            ->get();

        $data_pekebun_sudah_rekon = $data_pekebun_sudah_rekon->keyBy('id');

        foreach ($data_pekebun_sudah_rekon as $key => $value) {
            $data_pekebun_sudah_rekon[$key]->luas_lahan = 0.0000;
        }

        $get_data_legalitas = DB::connection('mysql_rdp')
                              ->table('legalitas_lahan_pekebun')
                              ->whereIn('id_pekebun',$list_id_pekebun_sudah_rekon)
                              ->where('id_proposal','=',$id_proposal)
                              ->get();
        foreach ($get_data_legalitas as $key => $value) {
            $data_pekebun_sudah_rekon[$value->id_pekebun]->luas_lahan += $value->luas_polygon_legalitas_lahan;
            if($value->polygon_legalitas_lahan === null){
                $data_pekebun_sudah_rekon[$value->id_pekebun]->tikor_zoom = "Tidak Bisa Zoom";
            }else{
                $list_tikor = json_decode($value->polygon_legalitas_lahan);
                $data_pekebun_sudah_rekon[$value->id_pekebun]->tikor_zoom = $list_tikor[0][1].",".$list_tikor[0][0];
            }
        }
        $legalitas_lahan_pekebun_milik_akun = DB::connection('mysql_rdp')
            ->table('legalitas_lahan_pekebun')
            ->join('proposal','proposal.id','=','legalitas_lahan_pekebun.id_proposal')
            ->join('kelembagaan_pekebun','kelembagaan_pekebun.id','=','proposal.id_kelembagaan_pekebun')
            ->join('pekebun','pekebun.id','=','legalitas_lahan_pekebun.id_pekebun')
            ->whereNull('legalitas_lahan_pekebun.deleted_at')
            ->where('kelembagaan_pekebun.id','=',$get_data_kelembagaan_pekebun->id_kelembagaan_pekebun)
            ->orderBy('proposal.nomor_proposal','ASC')
            ->select(
                'proposal.nomor_proposal',
                'pekebun.nama_pekebun',
                'legalitas_lahan_pekebun.nomor_dokumen_legalitas_lahan',
                'legalitas_lahan_pekebun.nama_tertera_dokumen_legalitas_lahan',
                'legalitas_lahan_pekebun.luas_polygon_legalitas_lahan',
                'legalitas_lahan_pekebun.polygon_legalitas_lahan',
            )->get();


        $legalitas_lahan_pekebun_proposal = [];
        $popup_legalitas_lahan_pekebun_proposal = [];
        foreach($legalitas_lahan_pekebun_milik_akun as $index_proposal=>$item_proposal){
            $polygon = json_decode($item_proposal->polygon_legalitas_lahan);
            $popup_legalitas_lahan_pekebun_proposal[$item_proposal->nomor_proposal][] = [
                'nama_pekebun' => $item_proposal->nama_pekebun,
                'nomor_dokumen_legalitas_lahan' => $item_proposal->nomor_dokumen_legalitas_lahan,
                'nama_tertera_dokumen_legalitas_lahan' => $item_proposal->nama_tertera_dokumen_legalitas_lahan,
                'luas_polygon_legalitas_lahan' => $item_proposal->luas_polygon_legalitas_lahan,
            ];
            $legalitas_lahan_pekebun_proposal[$item_proposal->nomor_proposal][] = $polygon;
            foreach($legalitas_lahan_pekebun_proposal[$item_proposal->nomor_proposal] as $index_legalitas_lahan=>$item_legalitas_lahan){
                if($item_legalitas_lahan === null){
                    continue;
                }
                foreach($item_legalitas_lahan as $index_polygon=>$item_polygon){
                    $legalitas_lahan_pekebun_proposal[$item_proposal->nomor_proposal][$index_legalitas_lahan][$index_polygon] = array_map('floatval', $item_polygon);
                }
            }
        }

        $legalitas_lahan_pekebun_lain = DB::connection('mysql_rdp')
            ->table('legalitas_lahan_pekebun')
            ->join('proposal','proposal.id','=','legalitas_lahan_pekebun.id_proposal')
            ->join('kelembagaan_pekebun','kelembagaan_pekebun.id','=','proposal.id_kelembagaan_pekebun')
            ->whereNull('legalitas_lahan_pekebun.deleted_at')
            ->whereNot('kelembagaan_pekebun.id','=',$get_data_kelembagaan_pekebun->id_kelembagaan_pekebun)
            ->select(
                'legalitas_lahan_pekebun.polygon_legalitas_lahan',
            )->get();


        $legalitas_lahan_pekebun_proposal_lain = [];
        foreach($legalitas_lahan_pekebun_lain as $index_polygon=>$item_polygon){
            $legalitas_lahan_pekebun_proposal_lain[$index_polygon] = json_decode($item_polygon->polygon_legalitas_lahan);
            if($legalitas_lahan_pekebun_proposal_lain[$index_polygon] === null){
                continue;
            }
            foreach($legalitas_lahan_pekebun_proposal_lain[$index_polygon] as $index_koordinat=>$item_koordinat){
                $legalitas_lahan_pekebun_proposal_lain[$index_polygon][$index_koordinat] = array_map('floatval', $item_koordinat);
            }
        }

        foreach ($legalitas_lahan_pekebun_proposal_lain as $key => $value) {
            if ($value === null) {
                unset($legalitas_lahan_pekebun_proposal_lain[$key]);
            }
        }

        $legalitas_lahan_pekebun_proposal_lain = array_values($legalitas_lahan_pekebun_proposal_lain);

        return view('tahap4.detail')->with(compact('get_data_kelembagaan_pekebun','data_pekebun_sudah_rekon','legalitas_lahan_pekebun_proposal_lain','legalitas_lahan_pekebun_proposal','popup_legalitas_lahan_pekebun_proposal'));
    }
}
