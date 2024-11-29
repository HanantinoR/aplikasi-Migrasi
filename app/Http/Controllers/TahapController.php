<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use DB;
use Response;
use \avadim\FastExcelWriter\Excel;

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
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi','tb_proposal.sk_penetapan_dirut','tb_koperasi.provinsi','tb_koperasi.kabupaten')
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
            $tanggal_lahir_pekebun = trim($get_data_pekebun_psr_online->tgl_lahir) == null ? NULL : trim($get_data_pekebun_psr_online->tgl_lahir);
            $id_pekebun = DB::connection('mysql_rdp')
            ->table('pekebun')
            ->insertGetId([
                "nik_pekebun" => trim($get_data_pekebun_psr_online->no_ktp) == null ? NULL : trim($get_data_pekebun_psr_online->no_ktp),
                "kk_pekebun" => trim($get_data_pekebun_psr_online->no_kk) == null ? NULL : trim($get_data_pekebun_psr_online->no_kk),
                "nama_pekebun" => strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->nama_pekebun)),
                "tanggal_lahir_pekebun" => $tanggal_lahir_pekebun === "0000-00-00" ? null : $tanggal_lahir_pekebun,
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

    public function excel_tahap_1(){
        $get_data_list_proposal = DB::connection('mysql_rdp')
                            ->table('lookup_proposal_kelembagaan_pekebun')
                            ->whereNotNull('id_proposal_smart_psr')
                            ->get();

        $list_id_proposal_psr_online = $get_data_list_proposal->pluck('id_proposal_psr_online');
        $list_id_proposal_smart_psr = $get_data_list_proposal->pluck('id_proposal_smart_psr');

        $data_pekebun_psr_online = DB::connection('mysql_psr')
                                ->table('tb_pekebun')
                                ->whereIn('id_proposal',$list_id_proposal_psr_online)
                                ->orderBy('id_proposal','ASC')
                                ->select('id_proposal',DB::raw('COUNT(id_proposal) as jumlah_pekebun_psr_online'))
                                ->groupBy('id_proposal')
                                ->get();

        $data_pekebun_psr_online = $data_pekebun_psr_online->keyBy('id_proposal');

        $data_pekebun_smart_psr = DB::connection('mysql_smart_psr')
                                ->table('tbl_master_pekebun')
                                ->whereIn('id_proposal',$list_id_proposal_smart_psr)
                                ->orderBy('id_proposal','ASC')
                                ->select('id_proposal',DB::raw('COUNT(id_proposal) as jumlah_pekebun_smart_psr'))
                                ->groupBy('id_proposal')
                                ->get();

        foreach ($data_pekebun_smart_psr as $key => $value) {
            $data_pekebun_smart_psr[$key]->id_proposal_psr_online = $get_data_list_proposal->where('id_proposal_smart_psr','=',$value->id_proposal)->value('id_proposal_psr_online');
        }

        $data_pekebun_smart_psr = $data_pekebun_smart_psr->keyBy('id_proposal_psr_online');

        $get_data_kelembagaan_pekebun = DB::connection('mysql_psr')
                                        ->table('tb_proposal')
                                        ->whereIn('tb_proposal.id_proposal',$list_id_proposal_psr_online)
                                        ->join('tb_koperasi','tb_koperasi.id_koperasi','=','tb_proposal.id_koperasi')
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi','sk_penetapan_dirut','tb_koperasi.provinsi','tb_koperasi.kabupaten','tb_proposal.tgl_sk_dirut')
                                        ->orderBy('tb_proposal.no_dokumen','ASC')
                                        ->get();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
                                ->table('lookup_pekebun_proposal')
                                ->select('id_proposal_psr_online',DB::raw('COUNT(id_proposal_psr_online) as jumlah_pekebun_psr_online_rekon'))
                                ->groupBy('id_proposal_psr_online')
                                ->get();

        $get_id_hasil_rekon = $get_id_hasil_rekon->keyBy('id_proposal_psr_online');

        $data_pekebun_sudah_rekon = DB::connection('mysql_rdp')
                            ->table('legalitas_lahan_pekebun')
                            ->join('proposal','proposal.id','=','legalitas_lahan_pekebun.id_proposal')
                            ->select('nomor_proposal','id_proposal',DB::raw('SUM(luas_polygon_legalitas_lahan) as total_lahan_rekon'))
                            ->groupBy('id_proposal','nomor_proposal')
                            ->get();

        foreach ($data_pekebun_sudah_rekon as $key => $value) {
            $data_pekebun_sudah_rekon[$key]->id_proposal_psr_online = $get_data_kelembagaan_pekebun->where('no_dokumen','=',$value->nomor_proposal)->value('id_proposal');
        }

        $data_pekebun_sudah_rekon = $data_pekebun_sudah_rekon->keyBy('id_proposal_psr_online');

        $nomor = 1;
        $data_excel = array();
        foreach ($get_data_kelembagaan_pekebun as $key => $value) {
            $get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_psr_online = $data_pekebun_psr_online[$value->id_proposal]->jumlah_pekebun_psr_online;
            $get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_smart_psr = $data_pekebun_smart_psr[$value->id_proposal]->jumlah_pekebun_smart_psr;
            @$get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_rekon = $get_id_hasil_rekon[$value->id_proposal]->jumlah_pekebun_psr_online_rekon;
            @$get_data_kelembagaan_pekebun[$key]->luas_lahan_rekon = $data_pekebun_sudah_rekon[$value->id_proposal]->total_lahan_rekon;
            if(strtotime($value->tgl_sk_dirut) < strtotime('2020-06-01')){
                $uang = 25000000;
            }else{
                $uang = 30000000;
            }
            @$dana_ppks = $data_pekebun_sudah_rekon[$value->id_proposal]->total_lahan_rekon * $uang;
            $data_excel[] = [$nomor++,$value->koperasi,$value->no_dokumen,$value->provinsi,$value->kabupaten,$get_data_kelembagaan_pekebun[$key]->luas_lahan_rekon,$dana_ppks,$get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_rekon,($get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_psr_online - $get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_rekon),($get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_smart_psr - $get_data_kelembagaan_pekebun[$key]->jumlah_pekebun_rekon)];
        }

        $excel = Excel::create(['Sheet1']);
        $sheet = $excel->sheet();
        $sheet->setColWidths(
            [
                5,75,17,36,38,18,19,15,20,25
            ]
        );
        // Write heads

        // Write data
        $colFormats = [
            '0',
            '@',
            '@',
            '@',
            '@',
            '#,##0.0000;[Red]-#,##0.0000',
            '_-[$Rp-3809]*' . chr(20) . '#,##0_-;[Red]-[$Rp-3809]*' . chr(20) . '#,##0_-;_-[$Rp-3809]*' . chr(20) . '"-"_-;_-@_-',
            '0',
            '0',
            '0'
        ];
        $sheet->setColFormats($colFormats);
        $sheet->writeRow(['No', 'Nama Kelembagaan Pekebun', 'Nomor Proposal','Provinsi','Kabupaten','Luas Lahan SK Dirut','Dana PPKS','Jumlah Pekebun','Outlier PSR Online (Kiri)','Outlier SMART-PSR (Kanan)']);

        foreach($data_excel as $rowData) {
            $sheet->writeRow($rowData);
        }
        $filename = 'Outstanding Rekon Pekebun Per '.date('Y_m_d H_i_s').'.xlsx';
        $excel->save($filename);

        return Response::download($filename)->deleteFileAfterSend(true);

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

        $get_nik_psr_online =    DB::connection('mysql_psr')
        ->table('tb_pekebun')
        ->where('id_pekebun','=',$get_id_hasil_rekon->id_pekebun_psr_online)
        ->value('no_ktp');

        $get_dokumen_psr_online =   DB::connection('mysql_psr')
        ->table('tb_pekebun')
        ->where('no_ktp','=',$get_nik_psr_online)
        ->orderBy('surat_kuasa','DESC')
        ->orderBy('fc_kk','DESC')
        ->orderBy('fc_ktp','DESC')
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
                            ->whereNotIn('proposal.nomor_proposal',["PRO1902130012","PRO1901230002","PRO1902040003","PRO1902010007","PRO1902010009","PRO1902040001","PRO1902010006","PRO1901250002","PRO1902010010","PRO1901240003","PRO1901240001","PRO1902150011","PRO1901310008","PRO1901240004","PRO1902120001","PRO1902060001","PRO1901240002","PRO1901230003","PRO1902010002","PRO1902010011","PRO1902040004","PRO1902220012","PRO1903050001","PRO1902120008","PRO1902120012","PRO1902150015","PRO1901250001","PRO1902250001","PRO1902110003","PRO1902040002","PRO1902180003","PRO1902120006","PRO1902250004","PRO1902150008","PRO1902260001","PRO1901280001","PRO1903200001","PRO1902220008","PRO1902250003","PRO1902150012","PRO1902220003","PRO1901230001","PRO1902200015","PRO1902150014","PRO1902200020","PRO1903010011","PRO1902190003","PRO1902150005","PRO1902200006","PRO1902270005","PRO1902150004","PRO1902150010","PRO1902150002","PRO1902080002","PRO1902150009","PRO1902140005","PRO1902260002","PRO1902110006","PRO1902260003","PRO1902140003","PRO1902210004","PRO1901250007","PRO1902210003","PRO1902190011","PRO1902140007","PRO1902200010","PRO1902260004","PRO1902180009","PRO1902120003","PRO1901250004","PRO1902010003","PRO1901310002","PRO1902010005","PRO1902220010","PRO1902220007","PRO1901280003","PRO1907080008","PRO1902010001","PRO1902110001","PRO1902220011","PRO1902150006","PRO1902200016","PRO1903010005","PRO1902250006","PRO1902130013","PRO1902220009","PRO1901300002","PRO1902080003","PRO1903010012","PRO1903010002","PRO1901290002","PRO1902130011","PRO1902220015","PRO1902010004","PRO1902110004","PRO1902200008","PRO1902200017","PRO1902200018","PRO1903010013","PRO1902130008","PRO1902200014","PRO1902150001","PRO1903010008","PRO1902200011","PRO1902250005","PRO1902200005","PRO1901300005","PRO1902280008","PRO1901300004","PRO1902130002","PRO1902280002","PRO1901270001","PRO1902200012","PRO1902270002","PRO1902260005","PRO1902080001","PRO1902150007","PRO1902130001","PRO1902270009","PRO1902120020","PRO1901250006","PRO1902180001","PRO1902190010","PRO1902270008","PRO1902190006","PRO1906250001","PRO1902070001","PRO1902260009","PRO1902120016","PRO1902280001","PRO1903010010","PRO1902130005","PRO1902270001","PRO1902260010","PRO1902260006","PRO1902220004","PRO1902120005","PRO1902200002","PRO1901310004","PRO1902260011","PRO1902220013","PRO1902260008","PRO1902200001","PRO1902070003","PRO1902280007","PRO1902140001","PRO1902180004","PRO1901250003","PRO1902210002","PRO1902200003","PRO1903010001","PRO1902250007","PRO1902190001","PRO1902140004","PRO1906250002","PRO1902150013","PRO1903010003","PRO1902200004","PRO1902190002","PRO1902120018","PRO1902190004","PRO1901310001","PRO1902210001","PRO1902220014","PRO1902130014","PRO1902220005","PRO1902120015","PRO1902120010","PRO1902270006","PRO1902120019","PRO1902200009","PRO1902180002","PRO1902120011","PRO1902180008","PRO1902220002","PRO1903010006","PRO1903190001","PRO1902220001","PRO1902200019","PRO1902280004","PRO1901250005","PRO1902110007","PRO1902120007","PRO1902130006","PRO1902110005","PRO1902180007","PRO1902190007","PRO1902120017","PRO1902180005","PRO1902190005","PRO1902190009","PRO1902180006","PRO1902130004","PRO1902130007","PRO1902140008","PRO1902130009","PRO1902280003","PRO1902120013","PRO1904220001","PRO1901300003","PRO1902130010","PRO1902190008","PRO1902010008","PRO1902120009","PRO1902200021","PRO1902120014","PRO1902130003","PRO1902220006","PRO1901310006","PRO1902120004","PRO1902140006","PRO1902270003","PRO1902110008","PRO1901310007","PRO1902250002","PRO1902270007","PRO1902200007","PRO1907270002","PRO1905150001","PRO1907030003","PRO1911240002","PRO1911200001","PRO1906210023","PRO1907090002","PRO1907100001","PRO1906210024","PRO1906200016","PRO1906210025","PRO1904260003","PRO1907040005","PRO1907090012","PRO1907110005","PRO1910020003","PRO1907050009","PRO1903140001","PRO1905160004","PRO1906280004","PRO1906210049","PRO1908160009","PRO1907020007","PRO1906210100","PRO1907060004","PRO1904240013","PRO1907110003","PRO1911020001","PRO1907090001","PRO1910080014","PRO1906210011","PRO1909200002","PRO1904240045","PRO1906210082","PRO1906210022","PRO1906210026","PRO1908200001","PRO1908270003","PRO1903140013","PRO1907310003","PRO1907110014","PRO1906180012","PRO1907080001","PRO1908100002","PRO1904160001","PRO1907040004","PRO1907230007","PRO1907110004","PRO1907100005","PRO1908260002","PRO1909080001","PRO1901250008","PRO1907290003","PRO1906210014","PRO1907090005","PRO1906210093","PRO1908150001","PRO1908150002","PRO1907120001","PRO1906210070","PRO1906190007","PRO1910240001","PRO1908210038","PRO1907090008","PRO1906210089","PRO1908210030","PRO1907050003","PRO1909160005","PRO1908200003","PRO1908210041","PRO1908210040","PRO1907090006","PRO1911300004","PRO1911090001","PRO1907260014","PRO1907090007","PRO1907100003","PRO1908150003","PRO1905020002","PRO1907260012","PRO1909160002","PRO1908120001","PRO1908280001","PRO1904230007","PRO1909030005","PRO1907120006","PRO1908080008","PRO1905250001","PRO1906280002","PRO1909200001","PRO1906190017","PRO1906210031","PRO1907160006","PRO1907100002","PRO1908150014","PRO1906110002","PRO1904250001","PRO1907110012","PRO1911060004","PRO1907120002","PRO1907250020","PRO1907260005","PRO1906210062","PRO1906210099","PRO1907110008","PRO1910150002","PRO1910030001","PRO1907230003","PRO1907030001","PRO1910100001","PRO1907110015","PRO1904240002","PRO1910290001","PRO1906210051","PRO1906170004","PRO1911210002","PRO1908160002","PRO1907250018","PRO1907080002","PRO1906200015","PRO1906210074","PRO1908050004","PRO1906210084","PRO1907110002","PRO1906210067","PRO1906190002","PRO1904260001","PRO1908220008","PRO1909160001","PRO1906210071","PRO1910160002","PRO1907310002","PRO1907230004","PRO1906110006","PRO1909200006","PRO1912060003","PRO1907290007","PRO1910160001","PRO1908150006","PRO1907030002","PRO1908200014","PRO1906180007","PRO1910290005","PRO1907110011","PRO1909020001","PRO1906200007","PRO1910180004","PRO1906210068","PRO1912040006","PRO1908090003","PRO1911270003","PRO1906190008","PRO1910230008","PRO1911260002","PRO1908080015","PRO1905020001","PRO1906210052","PRO1907260016","PRO1905030007","PRO1911220002","PRO1907230001","PRO1910180005","PRO1906190005","PRO1906210039","PRO1907060002","PRO1908210001","PRO1906180013","PRO1910280003","PRO1907060006","PRO1906190010","PRO1909050001","PRO1907270001","PRO1907260011","PRO1906180011","PRO1910290003","PRO1907250022","PRO1909120007","PRO1902030002","PRO1909050006","PRO1908150009","PRO1906210076","PRO1909060001","PRO1907240007","PRO1906200008","PRO1906210048","PRO1909270003","PRO1903170003","PRO1911080003","PRO1909240001","PRO1908160008","PRO1906210008","PRO1906280001","PRO1906210003","PRO1907260015","PRO1904240015","PRO1903010004","PRO1907230010","PRO1908090001","PRO1907300004","PRO1907300005","PRO1907070002","PRO1909040002","PRO1906180001","PRO1906190012","PRO1907090014","PRO1907040007","PRO1908150011","PRO1909130005","PRO1909260001","PRO1910260003","PRO1907080004","PRO1908080007","PRO1911270001","PRO1904250003","PRO1911200002","PRO1912050001","PRO1908270007","PRO1906210060","PRO1909270001","PRO1907240008","PRO1907250023","PRO1908200002","PRO1907040002","PRO1908280002","PRO1907250021","PRO1906190001","PRO1907260009","PRO1906210085","PRO1911250003","PRO1908270002","PRO1910160005","PRO1907300006","PRO1912060005","PRO1908100001","PRO1911220004","PRO1907080003","PRO1910040002","PRO1907160003","PRO1907110001","PRO1908290004","PRO1909230002","PRO1910300001","PRO1907290001","PRO1910200001","PRO1909240003","PRO1909030009","PRO1907260004","PRO1908190001","PRO1909040001","PRO1906260001","PRO1906190013","PRO1910260001","PRO1910290006","PRO1910300007","PRO1908140003","PRO1910300003","PRO1907060003","PRO1906210044","PRO1910250002","PRO1907260001","PRO1911220003","PRO1909180006","PRO1909200004","PRO1907080005","PRO1909140002","PRO1910230007","PRO1906210036","PRO1909160003","PRO1908210039","PRO1906210056","PRO1907110016","PRO1910070002","PRO1910040001","PRO1902200013","PRO1906210035","PRO1910090001","PRO1910230005","PRO1908210035","PRO1908130002","PRO1906210080","PRO1910300006","PRO1908280003","PRO1906210098","PRO1909120002","PRO1908210036","PRO1909300006","PRO1906210046","PRO1909250001","PRO1908080014","PRO1907090013","PRO1909180005","PRO1908160007","PRO1908290001","PRO1906210054","PRO1907250001","PRO1906210045","PRO1907160002","PRO1906210092","PRO1907110009","PRO1904240047","PRO1906210094","PRO1907230002","PRO1909140001","PRO1911190002","PRO1910310001","PRO1906180002","PRO1908300001","PRO1909120001","PRO1907120003","PRO1906210033","PRO1907290004","PRO1907260002","PRO1910280004","PRO1909250003","PRO1906210055","PRO1908270006","PRO1908200016","PRO1910180002","PRO1909180004","PRO1907280001","PRO1911020002","PRO1910180003","PRO1907090010","PRO1910170001","PRO1906210029","PRO1903060001","PRO1906170001","PRO1911140001","PRO1907290005","PRO1908160005","PRO1911080001","PRO1911150004","PRO1907300008","PRO1909230001","PRO1908270004","PRO1910010002","PRO1912050003","PRO1909120003","PRO1909140003","PRO1910020004","PRO1906210061","PRO1906170005","PRO1906210016","PRO1907090009","PRO1904240040","PRO1908260001","PRO1908160001","PRO1907290009","PRO1908200005","PRO1912040001","PRO1907240006","PRO1910280002","PRO1909120005","PRO1906210086","PRO1906210001","PRO1908230003","PRO1907020004","PRO1909020002","PRO1907260007","PRO1904240041","PRO1907050008","PRO1907260003","PRO1910250004","PRO1907120004","PRO1910110002","PRO1908150004","PRO1908150005","PRO1907310001","PRO1907260006","PRO1906110003","PRO1910300005","PRO1908140002","PRO1908220001","PRO1911290003","PRO1907100007","PRO1903130003","PRO1906210075","PRO1908290005","PRO1908070002","PRO1909270002","PRO1907230008","PRO1908160004","PRO1909040003","PRO1908210031","PRO1910280001","PRO1907050001","PRO1909110001","PRO1907050007","PRO1908210011","PRO1907060005","PRO1908310002","PRO1910230009","PRO1911160002","PRO1906180008","PRO1908190003","PRO1909150001","PRO1906180010","PRO1906110005","PRO1906210063","PRO1910070001","PRO1909180008","PRO1908150007","PRO1906190015","PRO1910230006","PRO1907290008","PRO1909090002","PRO1906190006","PRO1908080013","PRO1908200006","PRO1906210020","PRO1908050002","PRO1908060001","PRO1907120005","PRO1906110001","PRO1909170001","PRO1911190001","PRO1906190003","PRO1910260002","PRO1906200001","PRO1910230001","PRO1906200004","PRO1907050005","PRO1911220006","PRO1907100006","PRO1907110007","PRO1904260002","PRO1908040001","PRO1907260010","PRO1906170007","PRO1909240004","PRO1906210043","PRO1908210002","PRO1907040006","PRO1911260001","PRO1906210021","PRO1906210018","PRO1907110013","PRO1908270005","PRO1908130008","PRO1910100002","PRO1910180001","PRO1907090016","PRO1906210065","PRO1911080002","PRO1906210006","PRO1908200004","PRO1906210079","PRO1906210042","PRO1906110004","PRO1904240038","PRO1906200005","PRO1907290006","PRO1911210001","PRO1911240003","PRO1906200006","PRO1908230001","PRO1907090011","PRO1909170002","PRO1909260003","PRO1910030002","PRO1910160006","PRO1909260004","PRO1907050006","PRO1906210015","PRO1910210001","PRO1906200002","PRO1908220007","PRO1909030008","PRO1907040008","PRO1911060001","PRO1909110002","PRO1907240004","PRO1910190001","PRO1906170002","PRO1907300001","PRO1907300007","PRO1909220001","PRO1907090004","PRO1906140008","PRO1910240002","PRO1906200003","PRO1907230005","PRO1906170003","PRO1910150001","PRO1911290001","PRO1907050002","PRO1911120002","PRO1907060001","PRO1908270008","PRO1911140017","PRO1910230002","PRO1912040004","PRO1908290006","PRO1907090015","PRO1907100004","PRO1909200003","PRO1904150001","PRO1905080001","PRO1908090002","PRO1909240002","PRO1906170006","PRO1909050003","PRO1908210003","PRO1909190002","PRO2004020002","PRO2004080003","PRO2010200002","PRO1905160006","PRO2002120001","PRO2004280003","PRO2006040001","PRO2010240004","PRO2001220001","PRO2005110004","PRO2007150002","PRO2011130004","PRO2002100001","PRO2004230005","PRO1908080012","PRO1906210066","PRO2002230001","PRO2003310001","PRO1908080019","PRO2007230003","PRO2005160001","PRO1911150006","PRO1909130003","PRO1909130004","PRO2003270001","PRO1907160005","PRO2004070001","PRO2001080003","PRO2003120003","PRO2005090001","PRO2006290001","PRO1910220002","PRO1912060002","PRO2005110002","PRO1911260003","PRO2003080002","PRO2001250002","PRO2005150001","PRO1906280003","PRO2001130001","PRO1903170001","PRO1911110001","PRO2001310001","PRO2001180001","PRO2006050004","PRO1909120006","PRO2007300003","PRO2005120001","PRO1910010004","PRO1912030003","PRO2006010001","PRO2002040002","PRO2003100005","PRO2004200001","PRO1902240001","PRO1906210078","PRO2007300008","PRO2008260001","PRO2004110002","PRO2005080002","PRO2003190002","PRO2001030001","PRO2004260004","PRO1905160002","PRO2005150003","PRO2001300004","PRO1907050004","PRO1912020004","PRO2004130003","PRO1906220001","PRO2002050001","PRO2011270003","PRO2008190003","PRO2003170003","PRO1907040003","PRO2008290003","PRO2003080001","PRO2008270004","PRO2006200004","PRO2006180002","PRO2012010002","PRO1906180006","PRO2009030004","PRO2007200005","PRO2007150001","PRO2006020001","PRO2006300009","PRO2006300008","PRO2002200001","PRO2009270002","PRO2004200003","PRO2003220002","PRO2004050001","PRO2003100004","PRO2007280003","PRO2006180007","PRO1909050004","PRO2007230005","PRO2004180001","PRO2004230001","PRO2005220003","PRO1906210072","PRO2004080001","PRO2004010005","PRO2007140001","PRO2005140002","PRO1910290002","PRO1912020003","PRO2009020005","PRO2007010005","PRO2002180001","PRO2007290004","PRO1911150003","PRO2008310003","PRO2003240001","PRO1911250002","PRO1904300005","PRO2003190001","PRO2004060002","PRO2010270003","PRO2007280004","PRO1907180001","PRO2002270001","PRO2006300003","PRO2003250001","PRO2010130008","PRO2003230003","PRO1910010005","PRO2006170001","PRO2003300001","PRO2005050004","PRO2007070004","PRO2010090005","PRO2005120003","PRO2009220002","PRO2006300010","PRO2006300011","PRO2004300001","PRO2012020003","PRO2005040002","PRO2007300009","PRO1904300008","PRO2004200002","PRO1911060002","PRO2004030001","PRO1912060001","PRO2004130002","PRO2003040001","PRO2010150004","PRO2004170002","PRO2008060002","PRO2003260002","PRO1911220005","PRO2002260002","PRO2004210002","PRO1912020001","PRO2004130001","PRO2007010001","PRO2004030004","PRO2006200001","PRO2002020001","PRO1906260002","PRO2004020003","PRO2009110002","PRO2007130002","PRO2002270004","PRO2007070001","PRO1908070003","PRO2001200001","PRO2002240001","PRO1909250002","PRO2004040001","PRO1910210002","PRO2007270004","PRO2004010003","PRO2006180005","PRO2002140001","PRO2008070003","PRO2008050005","PRO2011120002","PRO1910250001","PRO2012040001","PRO1907170002","PRO2006250006","PRO2006290005","PRO2005040001","PRO1911200007","PRO2010220004","PRO2003090002","PRO2009090010","PRO2006080001","PRO2011140003","PRO2009030007","PRO2006180008","PRO1910050002","PRO2002100002","PRO2002250003","PRO2007100001","PRO1911030001","PRO2001290004","PRO1911010001","PRO2004270004","PRO2005010001","PRO2006060002","PRO2004030003","PRO2006050005","PRO1911200008","PRO2002080001","PRO2003160001","PRO2004200008","PRO2005220002","PRO1912060006","PRO2004220003","PRO2004280007","PRO2008220002","PRO2004220004","PRO1911280001","PRO2010230004","PRO2001170001","PRO2008150004","PRO2008210002","PRO2004250002","PRO2007100002","PRO2004210004","PRO1911190003","PRO2007060003","PRO2006250001","PRO2009060002","PRO2009010006","PRO2007070002","PRO1912230002","PRO2003230002","PRO2006180001","PRO1909050002","PRO1912020002","PRO1910270002","PRO1910130001","PRO2010230003","PRO1912040002","PRO1908220003","PRO2009100007","PRO2008190002","PRO2003170005","PRO2007170004","PRO2007010002","PRO2008240002","PRO2006050002","PRO1906190009","PRO2001300007","PRO2006240001","PRO2011260001","PRO2006270002","PRO2006240003","PRO2002260001","PRO2004230002","PRO2011170004","PRO2004180002","PRO2007040001","PRO2008100003","PRO2002280001","PRO1906210087","PRO2009300001","PRO2003100001","PRO1911200003","PRO2006260003","PRO1912060007","PRO1907020006","PRO2003270002","PRO2007280006","PRO1911230001","PRO2004220001","PRO2001080002","PRO2006250004","PRO2006190001","PRO2004230004","PRO2007140002","PRO2007040002","PRO1908230002","PRO2006260004","PRO1912070003","PRO1912030002","PRO2010190003","PRO2005020001","PRO2001210001","PRO1911250001","PRO2004190003","PRO1911280002","PRO1910010001","PRO1907250024","PRO2004140001","PRO2003120004","PRO2004170003","PRO2003040003","PRO2003120005","PRO2005010002","PRO2007290002","PRO2004300002","PRO2001290002","PRO2008180002","PRO2005050001","PRO1910290004","PRO1909020003","PRO2003090003","PRO1901290001","PRO2009010007","PRO1908240002","PRO2006040002","PRO2004060003","PRO2006230003","PRO2004150003","PRO2009230001","PRO1909180002","PRO2005190001","PRO2006300004","PRO2004160002","PRO2009090007","PRO1912050002","PRO2004190002","PRO2004220005","PRO1909030001","PRO2004110001","PRO2007090001","PRO1908220010","PRO2007160003","PRO1911240001","PRO2008270005","PRO2001300001","PRO1910160007","PRO2008280001","PRO2004260002","PRO2003230004","PRO2010070008","PRO2004200005","PRO2003180001","PRO2003060001","PRO2003170002","PRO1909170003","PRO2003100003","PRO2005180001","PRO2005280001","PRO2009150004","PRO1909120004","PRO2006150002","PRO1908140001","PRO1908310001","PRO1909030007","PRO2003120001","PRO2004010002","PRO2001300006","PRO2006200003","PRO2005160002","PRO2006170002","PRO2009110007","PRO2004260001","PRO2004290004","PRO2002250002","PRO2004200007","PRO1911200006","PRO2007230001","PRO2007110001","PRO2007020001","PRO2004260003","PRO1911150002","PRO1907300003","PRO2005130001","PRO2007300004","PRO2004280002","PRO1911140014","PRO2005120005","PRO2007200002","PRO2004220002","PRO2009150003","PRO2009270003","PRO1908300002","PRO2004140002","PRO2008280003","PRO2005110003","PRO2008180001","PRO2005050003","PRO1908200013","PRO2001290001","PRO2006220003","PRO2011110004","PRO2010090002","PRO2002060001","PRO2005010004","PRO1910170002","PRO1912110002","PRO2007060001","PRO1907230013","PRO2011050001","PRO2005110001","PRO2001230002","PRO2011130003","PRO2002270003","PRO2005180002","PRO2009090001","PRO1908250001","PRO1910090003","PRO1912060004","PRO1911270002","PRO1912070002","PRO2008040004","PRO1912040005","PRO2005130003","PRO2002280002","PRO2009230005","PRO2007300005","PRO1911150005","PRO2004150002","PRO2011260010","PRO2002070001","PRO2006300007","PRO2002270005","PRO1910020002","PRO2004210001","PRO1910020005","PRO2001300002","PRO2008310001","PRO2002240002","PRO2009180001","PRO1909030003","PRO1909030004","PRO2008030002","PRO1910080003","PRO1910280005","PRO1911050006","PRO2007210003","PRO2003130001","PRO2004290002","PRO1910070005","PRO1907070001","PRO2004180003","PRO2005050002","PRO2006250002","PRO1912010001","PRO2006240007","PRO2004230003","PRO2011170001","PRO1910270001","PRO2004270003","PRO1911140011","PRO1912260001","PRO1912030005","PRO2005080001","PRO2001250001","PRO1909030006","PRO2005120004","PRO2005220001","PRO2002070002","PRO2009130003","PRO1911140003","PRO2006200005","PRO1909030002","PRO2004290005","PRO2005060001","PRO2004270002","PRO2008070001","PRO1910160004","PRO2004020001","PRO2004270001","PRO2001300003","PRO2003280001","PRO2001100001","PRO2004080002","PRO2009300004","PRO2006240004","PRO1909090001","PRO2009100004","PRO2009070001","PRO1908270001","PRO2007180002","PRO2004280005","PRO2004240001","PRO2003130002","PRO2010260005","PRO2003280002","PRO1910070004","PRO1907250019","PRO2007130004","PRO1910090002","PRO2007170001","PRO2008110003","PRO1911130001","PRO1911300003","PRO2006100001","PRO1908070004","PRO1912030001","PRO1910300004","PRO2011130005","PRO2009040004","PRO2006160001","PRO2006220001","PRO2009030001","PRO2009020003","PRO2011170002","PRO2006120001","PRO2007080001","PRO2011140001","PRO2003040002","PRO2006200002","PRO2011270005","PRO1911120001","PRO2010240003","PRO2003300002","PRO2012080001","PRO2101290002","PRO1905160003","PRO2011090003","PRO1909130002","PRO2101130002","PRO2102230001","PRO2104210001","PRO1911020003","PRO2007170005","PRO2010270004","PRO2010080001","PRO2006290003","PRO2105040002","PRO2007250001","PRO2004290001","PRO2104010002","PRO2008180003","PRO2011030002","PRO2103020001","PRO2004200006","PRO2107150001","PRO2102010003","PRO2010130006","PRO2012150002","PRO2007300006","PRO2009010003","PRO2104270002","PRO2103100001","PRO2006030001","PRO2104290001","PRO2007050001","PRO2011140002","PRO2008310002","PRO2101300001","PRO2012100001","PRO2010260002","PRO2008270006","PRO2012170002","PRO1910050001","PRO2006160002","PRO2005260001","PRO2103180002","PRO2010140001","PRO2004280006","PRO2012220003","PRO2009060003","PRO2010300004","PRO2011060001","PRO2012080004","PRO2008030001","PRO1908150015","PRO2011020008","PRO2009060001","PRO2008050004","PRO2101080005","PRO2012290003","PRO2108250001","PRO2010300001","PRO2007130005","PRO2103220001","PRO2004280004","PRO2106180002","PRO2009080004","PRO2005120002","PRO2011300008","PRO2006240006","PRO2009110001","PRO1908150016","PRO2008250003","PRO2108080002","PRO1907180006","PRO2101080006","PRO2011060002","PRO2010210001","PRO2002190003","PRO2010050004","PRO2106170006","PRO2105010003","PRO2102160001","PRO2103310002","PRO2009160002","PRO2011270001","PRO2101180003","PRO2011080001","PRO2012290005","PRO2008150003","PRO2103270003","PRO2109010003","PRO2009100003","PRO2104070002","PRO2106200002","PRO2010130003","PRO2003220001","PRO2011280003","PRO2102050001","PRO2108090001","PRO2011020003","PRO2011020003","PRO2003190003","PRO2009220003","PRO2107020002","PRO2010260006","PRO2109210001","PRO2106240002","PRO2005110005","PRO2012220001","PRO2101130001","PRO2010270010","PRO2009250003","PRO2010270006","PRO1907020003","PRO2003290001","PRO2010110002","PRO2008120002","PRO2012190001","PRO2006280001","PRO2108230001","PRO2004030005","PRO2012140001","PRO2007220003","PRO2101240001","PRO2011260006","PRO2103030003","PRO2010140003","PRO2008130002","PRO2009280005","PRO2006150001","PRO2007270001","PRO2012160001","PRO2104160002","PRO2101110002","PRO2009020006","PRO2012210004","PRO2010210003","PRO2012090002","PRO2010070004","PRO2012050003","PRO2103020004","PRO2010310005","PRO2010290001","PRO2012110001","PRO2011260007","PRO2007010003","PRO2101280001","PRO2010020001","PRO2012280003","PRO2011030008","PRO2009150001","PRO2109230004","PRO2108220001","PRO2012310003","PRO2102100002","PRO2104170002","PRO2102160002","PRO2105030001","PRO2104200001","PRO2011110002","PRO1907290002","PRO2012210001","PRO2105250001","PRO2109020002","PRO2012120002","PRO2007300007","PRO2101040001","PRO2005280002","PRO2009170007","PRO2103260002","PRO2106200004","PRO2007180001","PRO2101230003","PRO2101230004","PRO2011260004","PRO2009180002","PRO2101270006","PRO2106230001","PRO2003200001","PRO2007090002","PRO2012040002","PRO2008240001","PRO2011170005","PRO2010120002","PRO2106190001","PRO2009210005","PRO2103090001","PRO2104030001","PRO2009190002","PRO2009300006","PRO2009240003","PRO2010100002","PRO2009040002","PRO2009180005","PRO2009040001","PRO2103300003","PRO2009300005","PRO2009170003","PRO2010070007","PRO2008240004","PRO2010280002","PRO2007220001"])
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

        // dd($get_id_hasil_rekon);

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

        // dd($get_data_legalitas);
        foreach ($get_data_legalitas as $key => $value) {
            $data_pekebun_sudah_rekon[$value->id_pekebun]->luas_lahan += $value->luas_polygon_legalitas_lahan;
            $data_pekebun_sudah_rekon[$value->id_pekebun]->id_legalitas = $value->id;
            if($value->polygon_legalitas_lahan === null || $value->polygon_legalitas_lahan == ""){
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

        return view('tahap4.detail')->with(compact('id_proposal','get_data_kelembagaan_pekebun','data_pekebun_sudah_rekon','legalitas_lahan_pekebun_proposal_lain','legalitas_lahan_pekebun_proposal','popup_legalitas_lahan_pekebun_proposal'));
    }

    public function detail4pekebun($id_proposal,$id_legalitas)
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

        // Unique Nya Disini
        $get_data_legalitas_pekebun_selected = DB::connection('mysql_rdp')
                                                ->table('legalitas_lahan_pekebun')
                                                ->whereIn('id_pekebun',$list_id_pekebun_sudah_rekon)
                                                ->where('id_proposal','=',$id_proposal)
                                                ->where('id','=',$id_legalitas)
                                                ->first();

        $data_pekebun_selected = DB::connection('mysql_rdp')
                                ->table('pekebun')
                                ->where('id','=',$get_data_legalitas_pekebun_selected->id_pekebun)
                                ->first();

        // dd($get_data_legalitas_pekebun_selected);

        // dd($get_data_legalitas);
        foreach ($get_data_legalitas as $key => $value) {
            $data_pekebun_sudah_rekon[$value->id_pekebun]->luas_lahan += $value->luas_polygon_legalitas_lahan;
            if($value->polygon_legalitas_lahan === null || $value->polygon_legalitas_lahan == ""){
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
            ->whereNotIn('proposal.nomor_proposal',["PRO1902130012","PRO1901230002","PRO1902040003","PRO1902010007","PRO1902010009","PRO1902040001","PRO1902010006","PRO1901250002","PRO1902010010","PRO1901240003","PRO1901240001","PRO1902150011","PRO1901310008","PRO1901240004","PRO1902120001","PRO1902060001","PRO1901240002","PRO1901230003","PRO1902010002","PRO1902010011","PRO1902040004","PRO1902220012","PRO1903050001","PRO1902120008","PRO1902120012","PRO1902150015","PRO1901250001","PRO1902250001","PRO1902110003","PRO1902040002","PRO1902180003","PRO1902120006","PRO1902250004","PRO1902150008","PRO1902260001","PRO1901280001","PRO1903200001","PRO1902220008","PRO1902250003","PRO1902150012","PRO1902220003","PRO1901230001","PRO1902200015","PRO1902150014","PRO1902200020","PRO1903010011","PRO1902190003","PRO1902150005","PRO1902200006","PRO1902270005","PRO1902150004","PRO1902150010","PRO1902150002","PRO1902080002","PRO1902150009","PRO1902140005","PRO1902260002","PRO1902110006","PRO1902260003","PRO1902140003","PRO1902210004","PRO1901250007","PRO1902210003","PRO1902190011","PRO1902140007","PRO1902200010","PRO1902260004","PRO1902180009","PRO1902120003","PRO1901250004","PRO1902010003","PRO1901310002","PRO1902010005","PRO1902220010","PRO1902220007","PRO1901280003","PRO1907080008","PRO1902010001","PRO1902110001","PRO1902220011","PRO1902150006","PRO1902200016","PRO1903010005","PRO1902250006","PRO1902130013","PRO1902220009","PRO1901300002","PRO1902080003","PRO1903010012","PRO1903010002","PRO1901290002","PRO1902130011","PRO1902220015","PRO1902010004","PRO1902110004","PRO1902200008","PRO1902200017","PRO1902200018","PRO1903010013","PRO1902130008","PRO1902200014","PRO1902150001","PRO1903010008","PRO1902200011","PRO1902250005","PRO1902200005","PRO1901300005","PRO1902280008","PRO1901300004","PRO1902130002","PRO1902280002","PRO1901270001","PRO1902200012","PRO1902270002","PRO1902260005","PRO1902080001","PRO1902150007","PRO1902130001","PRO1902270009","PRO1902120020","PRO1901250006","PRO1902180001","PRO1902190010","PRO1902270008","PRO1902190006","PRO1906250001","PRO1902070001","PRO1902260009","PRO1902120016","PRO1902280001","PRO1903010010","PRO1902130005","PRO1902270001","PRO1902260010","PRO1902260006","PRO1902220004","PRO1902120005","PRO1902200002","PRO1901310004","PRO1902260011","PRO1902220013","PRO1902260008","PRO1902200001","PRO1902070003","PRO1902280007","PRO1902140001","PRO1902180004","PRO1901250003","PRO1902210002","PRO1902200003","PRO1903010001","PRO1902250007","PRO1902190001","PRO1902140004","PRO1906250002","PRO1902150013","PRO1903010003","PRO1902200004","PRO1902190002","PRO1902120018","PRO1902190004","PRO1901310001","PRO1902210001","PRO1902220014","PRO1902130014","PRO1902220005","PRO1902120015","PRO1902120010","PRO1902270006","PRO1902120019","PRO1902200009","PRO1902180002","PRO1902120011","PRO1902180008","PRO1902220002","PRO1903010006","PRO1903190001","PRO1902220001","PRO1902200019","PRO1902280004","PRO1901250005","PRO1902110007","PRO1902120007","PRO1902130006","PRO1902110005","PRO1902180007","PRO1902190007","PRO1902120017","PRO1902180005","PRO1902190005","PRO1902190009","PRO1902180006","PRO1902130004","PRO1902130007","PRO1902140008","PRO1902130009","PRO1902280003","PRO1902120013","PRO1904220001","PRO1901300003","PRO1902130010","PRO1902190008","PRO1902010008","PRO1902120009","PRO1902200021","PRO1902120014","PRO1902130003","PRO1902220006","PRO1901310006","PRO1902120004","PRO1902140006","PRO1902270003","PRO1902110008","PRO1901310007","PRO1902250002","PRO1902270007","PRO1902200007","PRO1907270002","PRO1905150001","PRO1907030003","PRO1911240002","PRO1911200001","PRO1906210023","PRO1907090002","PRO1907100001","PRO1906210024","PRO1906200016","PRO1906210025","PRO1904260003","PRO1907040005","PRO1907090012","PRO1907110005","PRO1910020003","PRO1907050009","PRO1903140001","PRO1905160004","PRO1906280004","PRO1906210049","PRO1908160009","PRO1907020007","PRO1906210100","PRO1907060004","PRO1904240013","PRO1907110003","PRO1911020001","PRO1907090001","PRO1910080014","PRO1906210011","PRO1909200002","PRO1904240045","PRO1906210082","PRO1906210022","PRO1906210026","PRO1908200001","PRO1908270003","PRO1903140013","PRO1907310003","PRO1907110014","PRO1906180012","PRO1907080001","PRO1908100002","PRO1904160001","PRO1907040004","PRO1907230007","PRO1907110004","PRO1907100005","PRO1908260002","PRO1909080001","PRO1901250008","PRO1907290003","PRO1906210014","PRO1907090005","PRO1906210093","PRO1908150001","PRO1908150002","PRO1907120001","PRO1906210070","PRO1906190007","PRO1910240001","PRO1908210038","PRO1907090008","PRO1906210089","PRO1908210030","PRO1907050003","PRO1909160005","PRO1908200003","PRO1908210041","PRO1908210040","PRO1907090006","PRO1911300004","PRO1911090001","PRO1907260014","PRO1907090007","PRO1907100003","PRO1908150003","PRO1905020002","PRO1907260012","PRO1909160002","PRO1908120001","PRO1908280001","PRO1904230007","PRO1909030005","PRO1907120006","PRO1908080008","PRO1905250001","PRO1906280002","PRO1909200001","PRO1906190017","PRO1906210031","PRO1907160006","PRO1907100002","PRO1908150014","PRO1906110002","PRO1904250001","PRO1907110012","PRO1911060004","PRO1907120002","PRO1907250020","PRO1907260005","PRO1906210062","PRO1906210099","PRO1907110008","PRO1910150002","PRO1910030001","PRO1907230003","PRO1907030001","PRO1910100001","PRO1907110015","PRO1904240002","PRO1910290001","PRO1906210051","PRO1906170004","PRO1911210002","PRO1908160002","PRO1907250018","PRO1907080002","PRO1906200015","PRO1906210074","PRO1908050004","PRO1906210084","PRO1907110002","PRO1906210067","PRO1906190002","PRO1904260001","PRO1908220008","PRO1909160001","PRO1906210071","PRO1910160002","PRO1907310002","PRO1907230004","PRO1906110006","PRO1909200006","PRO1912060003","PRO1907290007","PRO1910160001","PRO1908150006","PRO1907030002","PRO1908200014","PRO1906180007","PRO1910290005","PRO1907110011","PRO1909020001","PRO1906200007","PRO1910180004","PRO1906210068","PRO1912040006","PRO1908090003","PRO1911270003","PRO1906190008","PRO1910230008","PRO1911260002","PRO1908080015","PRO1905020001","PRO1906210052","PRO1907260016","PRO1905030007","PRO1911220002","PRO1907230001","PRO1910180005","PRO1906190005","PRO1906210039","PRO1907060002","PRO1908210001","PRO1906180013","PRO1910280003","PRO1907060006","PRO1906190010","PRO1909050001","PRO1907270001","PRO1907260011","PRO1906180011","PRO1910290003","PRO1907250022","PRO1909120007","PRO1902030002","PRO1909050006","PRO1908150009","PRO1906210076","PRO1909060001","PRO1907240007","PRO1906200008","PRO1906210048","PRO1909270003","PRO1903170003","PRO1911080003","PRO1909240001","PRO1908160008","PRO1906210008","PRO1906280001","PRO1906210003","PRO1907260015","PRO1904240015","PRO1903010004","PRO1907230010","PRO1908090001","PRO1907300004","PRO1907300005","PRO1907070002","PRO1909040002","PRO1906180001","PRO1906190012","PRO1907090014","PRO1907040007","PRO1908150011","PRO1909130005","PRO1909260001","PRO1910260003","PRO1907080004","PRO1908080007","PRO1911270001","PRO1904250003","PRO1911200002","PRO1912050001","PRO1908270007","PRO1906210060","PRO1909270001","PRO1907240008","PRO1907250023","PRO1908200002","PRO1907040002","PRO1908280002","PRO1907250021","PRO1906190001","PRO1907260009","PRO1906210085","PRO1911250003","PRO1908270002","PRO1910160005","PRO1907300006","PRO1912060005","PRO1908100001","PRO1911220004","PRO1907080003","PRO1910040002","PRO1907160003","PRO1907110001","PRO1908290004","PRO1909230002","PRO1910300001","PRO1907290001","PRO1910200001","PRO1909240003","PRO1909030009","PRO1907260004","PRO1908190001","PRO1909040001","PRO1906260001","PRO1906190013","PRO1910260001","PRO1910290006","PRO1910300007","PRO1908140003","PRO1910300003","PRO1907060003","PRO1906210044","PRO1910250002","PRO1907260001","PRO1911220003","PRO1909180006","PRO1909200004","PRO1907080005","PRO1909140002","PRO1910230007","PRO1906210036","PRO1909160003","PRO1908210039","PRO1906210056","PRO1907110016","PRO1910070002","PRO1910040001","PRO1902200013","PRO1906210035","PRO1910090001","PRO1910230005","PRO1908210035","PRO1908130002","PRO1906210080","PRO1910300006","PRO1908280003","PRO1906210098","PRO1909120002","PRO1908210036","PRO1909300006","PRO1906210046","PRO1909250001","PRO1908080014","PRO1907090013","PRO1909180005","PRO1908160007","PRO1908290001","PRO1906210054","PRO1907250001","PRO1906210045","PRO1907160002","PRO1906210092","PRO1907110009","PRO1904240047","PRO1906210094","PRO1907230002","PRO1909140001","PRO1911190002","PRO1910310001","PRO1906180002","PRO1908300001","PRO1909120001","PRO1907120003","PRO1906210033","PRO1907290004","PRO1907260002","PRO1910280004","PRO1909250003","PRO1906210055","PRO1908270006","PRO1908200016","PRO1910180002","PRO1909180004","PRO1907280001","PRO1911020002","PRO1910180003","PRO1907090010","PRO1910170001","PRO1906210029","PRO1903060001","PRO1906170001","PRO1911140001","PRO1907290005","PRO1908160005","PRO1911080001","PRO1911150004","PRO1907300008","PRO1909230001","PRO1908270004","PRO1910010002","PRO1912050003","PRO1909120003","PRO1909140003","PRO1910020004","PRO1906210061","PRO1906170005","PRO1906210016","PRO1907090009","PRO1904240040","PRO1908260001","PRO1908160001","PRO1907290009","PRO1908200005","PRO1912040001","PRO1907240006","PRO1910280002","PRO1909120005","PRO1906210086","PRO1906210001","PRO1908230003","PRO1907020004","PRO1909020002","PRO1907260007","PRO1904240041","PRO1907050008","PRO1907260003","PRO1910250004","PRO1907120004","PRO1910110002","PRO1908150004","PRO1908150005","PRO1907310001","PRO1907260006","PRO1906110003","PRO1910300005","PRO1908140002","PRO1908220001","PRO1911290003","PRO1907100007","PRO1903130003","PRO1906210075","PRO1908290005","PRO1908070002","PRO1909270002","PRO1907230008","PRO1908160004","PRO1909040003","PRO1908210031","PRO1910280001","PRO1907050001","PRO1909110001","PRO1907050007","PRO1908210011","PRO1907060005","PRO1908310002","PRO1910230009","PRO1911160002","PRO1906180008","PRO1908190003","PRO1909150001","PRO1906180010","PRO1906110005","PRO1906210063","PRO1910070001","PRO1909180008","PRO1908150007","PRO1906190015","PRO1910230006","PRO1907290008","PRO1909090002","PRO1906190006","PRO1908080013","PRO1908200006","PRO1906210020","PRO1908050002","PRO1908060001","PRO1907120005","PRO1906110001","PRO1909170001","PRO1911190001","PRO1906190003","PRO1910260002","PRO1906200001","PRO1910230001","PRO1906200004","PRO1907050005","PRO1911220006","PRO1907100006","PRO1907110007","PRO1904260002","PRO1908040001","PRO1907260010","PRO1906170007","PRO1909240004","PRO1906210043","PRO1908210002","PRO1907040006","PRO1911260001","PRO1906210021","PRO1906210018","PRO1907110013","PRO1908270005","PRO1908130008","PRO1910100002","PRO1910180001","PRO1907090016","PRO1906210065","PRO1911080002","PRO1906210006","PRO1908200004","PRO1906210079","PRO1906210042","PRO1906110004","PRO1904240038","PRO1906200005","PRO1907290006","PRO1911210001","PRO1911240003","PRO1906200006","PRO1908230001","PRO1907090011","PRO1909170002","PRO1909260003","PRO1910030002","PRO1910160006","PRO1909260004","PRO1907050006","PRO1906210015","PRO1910210001","PRO1906200002","PRO1908220007","PRO1909030008","PRO1907040008","PRO1911060001","PRO1909110002","PRO1907240004","PRO1910190001","PRO1906170002","PRO1907300001","PRO1907300007","PRO1909220001","PRO1907090004","PRO1906140008","PRO1910240002","PRO1906200003","PRO1907230005","PRO1906170003","PRO1910150001","PRO1911290001","PRO1907050002","PRO1911120002","PRO1907060001","PRO1908270008","PRO1911140017","PRO1910230002","PRO1912040004","PRO1908290006","PRO1907090015","PRO1907100004","PRO1909200003","PRO1904150001","PRO1905080001","PRO1908090002","PRO1909240002","PRO1906170006","PRO1909050003","PRO1908210003","PRO1909190002","PRO2004020002","PRO2004080003","PRO2010200002","PRO1905160006","PRO2002120001","PRO2004280003","PRO2006040001","PRO2010240004","PRO2001220001","PRO2005110004","PRO2007150002","PRO2011130004","PRO2002100001","PRO2004230005","PRO1908080012","PRO1906210066","PRO2002230001","PRO2003310001","PRO1908080019","PRO2007230003","PRO2005160001","PRO1911150006","PRO1909130003","PRO1909130004","PRO2003270001","PRO1907160005","PRO2004070001","PRO2001080003","PRO2003120003","PRO2005090001","PRO2006290001","PRO1910220002","PRO1912060002","PRO2005110002","PRO1911260003","PRO2003080002","PRO2001250002","PRO2005150001","PRO1906280003","PRO2001130001","PRO1903170001","PRO1911110001","PRO2001310001","PRO2001180001","PRO2006050004","PRO1909120006","PRO2007300003","PRO2005120001","PRO1910010004","PRO1912030003","PRO2006010001","PRO2002040002","PRO2003100005","PRO2004200001","PRO1902240001","PRO1906210078","PRO2007300008","PRO2008260001","PRO2004110002","PRO2005080002","PRO2003190002","PRO2001030001","PRO2004260004","PRO1905160002","PRO2005150003","PRO2001300004","PRO1907050004","PRO1912020004","PRO2004130003","PRO1906220001","PRO2002050001","PRO2011270003","PRO2008190003","PRO2003170003","PRO1907040003","PRO2008290003","PRO2003080001","PRO2008270004","PRO2006200004","PRO2006180002","PRO2012010002","PRO1906180006","PRO2009030004","PRO2007200005","PRO2007150001","PRO2006020001","PRO2006300009","PRO2006300008","PRO2002200001","PRO2009270002","PRO2004200003","PRO2003220002","PRO2004050001","PRO2003100004","PRO2007280003","PRO2006180007","PRO1909050004","PRO2007230005","PRO2004180001","PRO2004230001","PRO2005220003","PRO1906210072","PRO2004080001","PRO2004010005","PRO2007140001","PRO2005140002","PRO1910290002","PRO1912020003","PRO2009020005","PRO2007010005","PRO2002180001","PRO2007290004","PRO1911150003","PRO2008310003","PRO2003240001","PRO1911250002","PRO1904300005","PRO2003190001","PRO2004060002","PRO2010270003","PRO2007280004","PRO1907180001","PRO2002270001","PRO2006300003","PRO2003250001","PRO2010130008","PRO2003230003","PRO1910010005","PRO2006170001","PRO2003300001","PRO2005050004","PRO2007070004","PRO2010090005","PRO2005120003","PRO2009220002","PRO2006300010","PRO2006300011","PRO2004300001","PRO2012020003","PRO2005040002","PRO2007300009","PRO1904300008","PRO2004200002","PRO1911060002","PRO2004030001","PRO1912060001","PRO2004130002","PRO2003040001","PRO2010150004","PRO2004170002","PRO2008060002","PRO2003260002","PRO1911220005","PRO2002260002","PRO2004210002","PRO1912020001","PRO2004130001","PRO2007010001","PRO2004030004","PRO2006200001","PRO2002020001","PRO1906260002","PRO2004020003","PRO2009110002","PRO2007130002","PRO2002270004","PRO2007070001","PRO1908070003","PRO2001200001","PRO2002240001","PRO1909250002","PRO2004040001","PRO1910210002","PRO2007270004","PRO2004010003","PRO2006180005","PRO2002140001","PRO2008070003","PRO2008050005","PRO2011120002","PRO1910250001","PRO2012040001","PRO1907170002","PRO2006250006","PRO2006290005","PRO2005040001","PRO1911200007","PRO2010220004","PRO2003090002","PRO2009090010","PRO2006080001","PRO2011140003","PRO2009030007","PRO2006180008","PRO1910050002","PRO2002100002","PRO2002250003","PRO2007100001","PRO1911030001","PRO2001290004","PRO1911010001","PRO2004270004","PRO2005010001","PRO2006060002","PRO2004030003","PRO2006050005","PRO1911200008","PRO2002080001","PRO2003160001","PRO2004200008","PRO2005220002","PRO1912060006","PRO2004220003","PRO2004280007","PRO2008220002","PRO2004220004","PRO1911280001","PRO2010230004","PRO2001170001","PRO2008150004","PRO2008210002","PRO2004250002","PRO2007100002","PRO2004210004","PRO1911190003","PRO2007060003","PRO2006250001","PRO2009060002","PRO2009010006","PRO2007070002","PRO1912230002","PRO2003230002","PRO2006180001","PRO1909050002","PRO1912020002","PRO1910270002","PRO1910130001","PRO2010230003","PRO1912040002","PRO1908220003","PRO2009100007","PRO2008190002","PRO2003170005","PRO2007170004","PRO2007010002","PRO2008240002","PRO2006050002","PRO1906190009","PRO2001300007","PRO2006240001","PRO2011260001","PRO2006270002","PRO2006240003","PRO2002260001","PRO2004230002","PRO2011170004","PRO2004180002","PRO2007040001","PRO2008100003","PRO2002280001","PRO1906210087","PRO2009300001","PRO2003100001","PRO1911200003","PRO2006260003","PRO1912060007","PRO1907020006","PRO2003270002","PRO2007280006","PRO1911230001","PRO2004220001","PRO2001080002","PRO2006250004","PRO2006190001","PRO2004230004","PRO2007140002","PRO2007040002","PRO1908230002","PRO2006260004","PRO1912070003","PRO1912030002","PRO2010190003","PRO2005020001","PRO2001210001","PRO1911250001","PRO2004190003","PRO1911280002","PRO1910010001","PRO1907250024","PRO2004140001","PRO2003120004","PRO2004170003","PRO2003040003","PRO2003120005","PRO2005010002","PRO2007290002","PRO2004300002","PRO2001290002","PRO2008180002","PRO2005050001","PRO1910290004","PRO1909020003","PRO2003090003","PRO1901290001","PRO2009010007","PRO1908240002","PRO2006040002","PRO2004060003","PRO2006230003","PRO2004150003","PRO2009230001","PRO1909180002","PRO2005190001","PRO2006300004","PRO2004160002","PRO2009090007","PRO1912050002","PRO2004190002","PRO2004220005","PRO1909030001","PRO2004110001","PRO2007090001","PRO1908220010","PRO2007160003","PRO1911240001","PRO2008270005","PRO2001300001","PRO1910160007","PRO2008280001","PRO2004260002","PRO2003230004","PRO2010070008","PRO2004200005","PRO2003180001","PRO2003060001","PRO2003170002","PRO1909170003","PRO2003100003","PRO2005180001","PRO2005280001","PRO2009150004","PRO1909120004","PRO2006150002","PRO1908140001","PRO1908310001","PRO1909030007","PRO2003120001","PRO2004010002","PRO2001300006","PRO2006200003","PRO2005160002","PRO2006170002","PRO2009110007","PRO2004260001","PRO2004290004","PRO2002250002","PRO2004200007","PRO1911200006","PRO2007230001","PRO2007110001","PRO2007020001","PRO2004260003","PRO1911150002","PRO1907300003","PRO2005130001","PRO2007300004","PRO2004280002","PRO1911140014","PRO2005120005","PRO2007200002","PRO2004220002","PRO2009150003","PRO2009270003","PRO1908300002","PRO2004140002","PRO2008280003","PRO2005110003","PRO2008180001","PRO2005050003","PRO1908200013","PRO2001290001","PRO2006220003","PRO2011110004","PRO2010090002","PRO2002060001","PRO2005010004","PRO1910170002","PRO1912110002","PRO2007060001","PRO1907230013","PRO2011050001","PRO2005110001","PRO2001230002","PRO2011130003","PRO2002270003","PRO2005180002","PRO2009090001","PRO1908250001","PRO1910090003","PRO1912060004","PRO1911270002","PRO1912070002","PRO2008040004","PRO1912040005","PRO2005130003","PRO2002280002","PRO2009230005","PRO2007300005","PRO1911150005","PRO2004150002","PRO2011260010","PRO2002070001","PRO2006300007","PRO2002270005","PRO1910020002","PRO2004210001","PRO1910020005","PRO2001300002","PRO2008310001","PRO2002240002","PRO2009180001","PRO1909030003","PRO1909030004","PRO2008030002","PRO1910080003","PRO1910280005","PRO1911050006","PRO2007210003","PRO2003130001","PRO2004290002","PRO1910070005","PRO1907070001","PRO2004180003","PRO2005050002","PRO2006250002","PRO1912010001","PRO2006240007","PRO2004230003","PRO2011170001","PRO1910270001","PRO2004270003","PRO1911140011","PRO1912260001","PRO1912030005","PRO2005080001","PRO2001250001","PRO1909030006","PRO2005120004","PRO2005220001","PRO2002070002","PRO2009130003","PRO1911140003","PRO2006200005","PRO1909030002","PRO2004290005","PRO2005060001","PRO2004270002","PRO2008070001","PRO1910160004","PRO2004020001","PRO2004270001","PRO2001300003","PRO2003280001","PRO2001100001","PRO2004080002","PRO2009300004","PRO2006240004","PRO1909090001","PRO2009100004","PRO2009070001","PRO1908270001","PRO2007180002","PRO2004280005","PRO2004240001","PRO2003130002","PRO2010260005","PRO2003280002","PRO1910070004","PRO1907250019","PRO2007130004","PRO1910090002","PRO2007170001","PRO2008110003","PRO1911130001","PRO1911300003","PRO2006100001","PRO1908070004","PRO1912030001","PRO1910300004","PRO2011130005","PRO2009040004","PRO2006160001","PRO2006220001","PRO2009030001","PRO2009020003","PRO2011170002","PRO2006120001","PRO2007080001","PRO2011140001","PRO2003040002","PRO2006200002","PRO2011270005","PRO1911120001","PRO2010240003","PRO2003300002","PRO2012080001","PRO2101290002","PRO1905160003","PRO2011090003","PRO1909130002","PRO2101130002","PRO2102230001","PRO2104210001","PRO1911020003","PRO2007170005","PRO2010270004","PRO2010080001","PRO2006290003","PRO2105040002","PRO2007250001","PRO2004290001","PRO2104010002","PRO2008180003","PRO2011030002","PRO2103020001","PRO2004200006","PRO2107150001","PRO2102010003","PRO2010130006","PRO2012150002","PRO2007300006","PRO2009010003","PRO2104270002","PRO2103100001","PRO2006030001","PRO2104290001","PRO2007050001","PRO2011140002","PRO2008310002","PRO2101300001","PRO2012100001","PRO2010260002","PRO2008270006","PRO2012170002","PRO1910050001","PRO2006160002","PRO2005260001","PRO2103180002","PRO2010140001","PRO2004280006","PRO2012220003","PRO2009060003","PRO2010300004","PRO2011060001","PRO2012080004","PRO2008030001","PRO1908150015","PRO2011020008","PRO2009060001","PRO2008050004","PRO2101080005","PRO2012290003","PRO2108250001","PRO2010300001","PRO2007130005","PRO2103220001","PRO2004280004","PRO2106180002","PRO2009080004","PRO2005120002","PRO2011300008","PRO2006240006","PRO2009110001","PRO1908150016","PRO2008250003","PRO2108080002","PRO1907180006","PRO2101080006","PRO2011060002","PRO2010210001","PRO2002190003","PRO2010050004","PRO2106170006","PRO2105010003","PRO2102160001","PRO2103310002","PRO2009160002","PRO2011270001","PRO2101180003","PRO2011080001","PRO2012290005","PRO2008150003","PRO2103270003","PRO2109010003","PRO2009100003","PRO2104070002","PRO2106200002","PRO2010130003","PRO2003220001","PRO2011280003","PRO2102050001","PRO2108090001","PRO2011020003","PRO2011020003","PRO2003190003","PRO2009220003","PRO2107020002","PRO2010260006","PRO2109210001","PRO2106240002","PRO2005110005","PRO2012220001","PRO2101130001","PRO2010270010","PRO2009250003","PRO2010270006","PRO1907020003","PRO2003290001","PRO2010110002","PRO2008120002","PRO2012190001","PRO2006280001","PRO2108230001","PRO2004030005","PRO2012140001","PRO2007220003","PRO2101240001","PRO2011260006","PRO2103030003","PRO2010140003","PRO2008130002","PRO2009280005","PRO2006150001","PRO2007270001","PRO2012160001","PRO2104160002","PRO2101110002","PRO2009020006","PRO2012210004","PRO2010210003","PRO2012090002","PRO2010070004","PRO2012050003","PRO2103020004","PRO2010310005","PRO2010290001","PRO2012110001","PRO2011260007","PRO2007010003","PRO2101280001","PRO2010020001","PRO2012280003","PRO2011030008","PRO2009150001","PRO2109230004","PRO2108220001","PRO2012310003","PRO2102100002","PRO2104170002","PRO2102160002","PRO2105030001","PRO2104200001","PRO2011110002","PRO1907290002","PRO2012210001","PRO2105250001","PRO2109020002","PRO2012120002","PRO2007300007","PRO2101040001","PRO2005280002","PRO2009170007","PRO2103260002","PRO2106200004","PRO2007180001","PRO2101230003","PRO2101230004","PRO2011260004","PRO2009180002","PRO2101270006","PRO2106230001","PRO2003200001","PRO2007090002","PRO2012040002","PRO2008240001","PRO2011170005","PRO2010120002","PRO2106190001","PRO2009210005","PRO2103090001","PRO2104030001","PRO2009190002","PRO2009300006","PRO2009240003","PRO2010100002","PRO2009040002","PRO2009180005","PRO2009040001","PRO2103300003","PRO2009300005","PRO2009170003","PRO2010070007","PRO2008240004","PRO2010280002","PRO2007220001"])
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
            ->whereNotIn('proposal.nomor_proposal',["PRO1902130012","PRO1901230002","PRO1902040003","PRO1902010007","PRO1902010009","PRO1902040001","PRO1902010006","PRO1901250002","PRO1902010010","PRO1901240003","PRO1901240001","PRO1902150011","PRO1901310008","PRO1901240004","PRO1902120001","PRO1902060001","PRO1901240002","PRO1901230003","PRO1902010002","PRO1902010011","PRO1902040004","PRO1902220012","PRO1903050001","PRO1902120008","PRO1902120012","PRO1902150015","PRO1901250001","PRO1902250001","PRO1902110003","PRO1902040002","PRO1902180003","PRO1902120006","PRO1902250004","PRO1902150008","PRO1902260001","PRO1901280001","PRO1903200001","PRO1902220008","PRO1902250003","PRO1902150012","PRO1902220003","PRO1901230001","PRO1902200015","PRO1902150014","PRO1902200020","PRO1903010011","PRO1902190003","PRO1902150005","PRO1902200006","PRO1902270005","PRO1902150004","PRO1902150010","PRO1902150002","PRO1902080002","PRO1902150009","PRO1902140005","PRO1902260002","PRO1902110006","PRO1902260003","PRO1902140003","PRO1902210004","PRO1901250007","PRO1902210003","PRO1902190011","PRO1902140007","PRO1902200010","PRO1902260004","PRO1902180009","PRO1902120003","PRO1901250004","PRO1902010003","PRO1901310002","PRO1902010005","PRO1902220010","PRO1902220007","PRO1901280003","PRO1907080008","PRO1902010001","PRO1902110001","PRO1902220011","PRO1902150006","PRO1902200016","PRO1903010005","PRO1902250006","PRO1902130013","PRO1902220009","PRO1901300002","PRO1902080003","PRO1903010012","PRO1903010002","PRO1901290002","PRO1902130011","PRO1902220015","PRO1902010004","PRO1902110004","PRO1902200008","PRO1902200017","PRO1902200018","PRO1903010013","PRO1902130008","PRO1902200014","PRO1902150001","PRO1903010008","PRO1902200011","PRO1902250005","PRO1902200005","PRO1901300005","PRO1902280008","PRO1901300004","PRO1902130002","PRO1902280002","PRO1901270001","PRO1902200012","PRO1902270002","PRO1902260005","PRO1902080001","PRO1902150007","PRO1902130001","PRO1902270009","PRO1902120020","PRO1901250006","PRO1902180001","PRO1902190010","PRO1902270008","PRO1902190006","PRO1906250001","PRO1902070001","PRO1902260009","PRO1902120016","PRO1902280001","PRO1903010010","PRO1902130005","PRO1902270001","PRO1902260010","PRO1902260006","PRO1902220004","PRO1902120005","PRO1902200002","PRO1901310004","PRO1902260011","PRO1902220013","PRO1902260008","PRO1902200001","PRO1902070003","PRO1902280007","PRO1902140001","PRO1902180004","PRO1901250003","PRO1902210002","PRO1902200003","PRO1903010001","PRO1902250007","PRO1902190001","PRO1902140004","PRO1906250002","PRO1902150013","PRO1903010003","PRO1902200004","PRO1902190002","PRO1902120018","PRO1902190004","PRO1901310001","PRO1902210001","PRO1902220014","PRO1902130014","PRO1902220005","PRO1902120015","PRO1902120010","PRO1902270006","PRO1902120019","PRO1902200009","PRO1902180002","PRO1902120011","PRO1902180008","PRO1902220002","PRO1903010006","PRO1903190001","PRO1902220001","PRO1902200019","PRO1902280004","PRO1901250005","PRO1902110007","PRO1902120007","PRO1902130006","PRO1902110005","PRO1902180007","PRO1902190007","PRO1902120017","PRO1902180005","PRO1902190005","PRO1902190009","PRO1902180006","PRO1902130004","PRO1902130007","PRO1902140008","PRO1902130009","PRO1902280003","PRO1902120013","PRO1904220001","PRO1901300003","PRO1902130010","PRO1902190008","PRO1902010008","PRO1902120009","PRO1902200021","PRO1902120014","PRO1902130003","PRO1902220006","PRO1901310006","PRO1902120004","PRO1902140006","PRO1902270003","PRO1902110008","PRO1901310007","PRO1902250002","PRO1902270007","PRO1902200007","PRO1907270002","PRO1905150001","PRO1907030003","PRO1911240002","PRO1911200001","PRO1906210023","PRO1907090002","PRO1907100001","PRO1906210024","PRO1906200016","PRO1906210025","PRO1904260003","PRO1907040005","PRO1907090012","PRO1907110005","PRO1910020003","PRO1907050009","PRO1903140001","PRO1905160004","PRO1906280004","PRO1906210049","PRO1908160009","PRO1907020007","PRO1906210100","PRO1907060004","PRO1904240013","PRO1907110003","PRO1911020001","PRO1907090001","PRO1910080014","PRO1906210011","PRO1909200002","PRO1904240045","PRO1906210082","PRO1906210022","PRO1906210026","PRO1908200001","PRO1908270003","PRO1903140013","PRO1907310003","PRO1907110014","PRO1906180012","PRO1907080001","PRO1908100002","PRO1904160001","PRO1907040004","PRO1907230007","PRO1907110004","PRO1907100005","PRO1908260002","PRO1909080001","PRO1901250008","PRO1907290003","PRO1906210014","PRO1907090005","PRO1906210093","PRO1908150001","PRO1908150002","PRO1907120001","PRO1906210070","PRO1906190007","PRO1910240001","PRO1908210038","PRO1907090008","PRO1906210089","PRO1908210030","PRO1907050003","PRO1909160005","PRO1908200003","PRO1908210041","PRO1908210040","PRO1907090006","PRO1911300004","PRO1911090001","PRO1907260014","PRO1907090007","PRO1907100003","PRO1908150003","PRO1905020002","PRO1907260012","PRO1909160002","PRO1908120001","PRO1908280001","PRO1904230007","PRO1909030005","PRO1907120006","PRO1908080008","PRO1905250001","PRO1906280002","PRO1909200001","PRO1906190017","PRO1906210031","PRO1907160006","PRO1907100002","PRO1908150014","PRO1906110002","PRO1904250001","PRO1907110012","PRO1911060004","PRO1907120002","PRO1907250020","PRO1907260005","PRO1906210062","PRO1906210099","PRO1907110008","PRO1910150002","PRO1910030001","PRO1907230003","PRO1907030001","PRO1910100001","PRO1907110015","PRO1904240002","PRO1910290001","PRO1906210051","PRO1906170004","PRO1911210002","PRO1908160002","PRO1907250018","PRO1907080002","PRO1906200015","PRO1906210074","PRO1908050004","PRO1906210084","PRO1907110002","PRO1906210067","PRO1906190002","PRO1904260001","PRO1908220008","PRO1909160001","PRO1906210071","PRO1910160002","PRO1907310002","PRO1907230004","PRO1906110006","PRO1909200006","PRO1912060003","PRO1907290007","PRO1910160001","PRO1908150006","PRO1907030002","PRO1908200014","PRO1906180007","PRO1910290005","PRO1907110011","PRO1909020001","PRO1906200007","PRO1910180004","PRO1906210068","PRO1912040006","PRO1908090003","PRO1911270003","PRO1906190008","PRO1910230008","PRO1911260002","PRO1908080015","PRO1905020001","PRO1906210052","PRO1907260016","PRO1905030007","PRO1911220002","PRO1907230001","PRO1910180005","PRO1906190005","PRO1906210039","PRO1907060002","PRO1908210001","PRO1906180013","PRO1910280003","PRO1907060006","PRO1906190010","PRO1909050001","PRO1907270001","PRO1907260011","PRO1906180011","PRO1910290003","PRO1907250022","PRO1909120007","PRO1902030002","PRO1909050006","PRO1908150009","PRO1906210076","PRO1909060001","PRO1907240007","PRO1906200008","PRO1906210048","PRO1909270003","PRO1903170003","PRO1911080003","PRO1909240001","PRO1908160008","PRO1906210008","PRO1906280001","PRO1906210003","PRO1907260015","PRO1904240015","PRO1903010004","PRO1907230010","PRO1908090001","PRO1907300004","PRO1907300005","PRO1907070002","PRO1909040002","PRO1906180001","PRO1906190012","PRO1907090014","PRO1907040007","PRO1908150011","PRO1909130005","PRO1909260001","PRO1910260003","PRO1907080004","PRO1908080007","PRO1911270001","PRO1904250003","PRO1911200002","PRO1912050001","PRO1908270007","PRO1906210060","PRO1909270001","PRO1907240008","PRO1907250023","PRO1908200002","PRO1907040002","PRO1908280002","PRO1907250021","PRO1906190001","PRO1907260009","PRO1906210085","PRO1911250003","PRO1908270002","PRO1910160005","PRO1907300006","PRO1912060005","PRO1908100001","PRO1911220004","PRO1907080003","PRO1910040002","PRO1907160003","PRO1907110001","PRO1908290004","PRO1909230002","PRO1910300001","PRO1907290001","PRO1910200001","PRO1909240003","PRO1909030009","PRO1907260004","PRO1908190001","PRO1909040001","PRO1906260001","PRO1906190013","PRO1910260001","PRO1910290006","PRO1910300007","PRO1908140003","PRO1910300003","PRO1907060003","PRO1906210044","PRO1910250002","PRO1907260001","PRO1911220003","PRO1909180006","PRO1909200004","PRO1907080005","PRO1909140002","PRO1910230007","PRO1906210036","PRO1909160003","PRO1908210039","PRO1906210056","PRO1907110016","PRO1910070002","PRO1910040001","PRO1902200013","PRO1906210035","PRO1910090001","PRO1910230005","PRO1908210035","PRO1908130002","PRO1906210080","PRO1910300006","PRO1908280003","PRO1906210098","PRO1909120002","PRO1908210036","PRO1909300006","PRO1906210046","PRO1909250001","PRO1908080014","PRO1907090013","PRO1909180005","PRO1908160007","PRO1908290001","PRO1906210054","PRO1907250001","PRO1906210045","PRO1907160002","PRO1906210092","PRO1907110009","PRO1904240047","PRO1906210094","PRO1907230002","PRO1909140001","PRO1911190002","PRO1910310001","PRO1906180002","PRO1908300001","PRO1909120001","PRO1907120003","PRO1906210033","PRO1907290004","PRO1907260002","PRO1910280004","PRO1909250003","PRO1906210055","PRO1908270006","PRO1908200016","PRO1910180002","PRO1909180004","PRO1907280001","PRO1911020002","PRO1910180003","PRO1907090010","PRO1910170001","PRO1906210029","PRO1903060001","PRO1906170001","PRO1911140001","PRO1907290005","PRO1908160005","PRO1911080001","PRO1911150004","PRO1907300008","PRO1909230001","PRO1908270004","PRO1910010002","PRO1912050003","PRO1909120003","PRO1909140003","PRO1910020004","PRO1906210061","PRO1906170005","PRO1906210016","PRO1907090009","PRO1904240040","PRO1908260001","PRO1908160001","PRO1907290009","PRO1908200005","PRO1912040001","PRO1907240006","PRO1910280002","PRO1909120005","PRO1906210086","PRO1906210001","PRO1908230003","PRO1907020004","PRO1909020002","PRO1907260007","PRO1904240041","PRO1907050008","PRO1907260003","PRO1910250004","PRO1907120004","PRO1910110002","PRO1908150004","PRO1908150005","PRO1907310001","PRO1907260006","PRO1906110003","PRO1910300005","PRO1908140002","PRO1908220001","PRO1911290003","PRO1907100007","PRO1903130003","PRO1906210075","PRO1908290005","PRO1908070002","PRO1909270002","PRO1907230008","PRO1908160004","PRO1909040003","PRO1908210031","PRO1910280001","PRO1907050001","PRO1909110001","PRO1907050007","PRO1908210011","PRO1907060005","PRO1908310002","PRO1910230009","PRO1911160002","PRO1906180008","PRO1908190003","PRO1909150001","PRO1906180010","PRO1906110005","PRO1906210063","PRO1910070001","PRO1909180008","PRO1908150007","PRO1906190015","PRO1910230006","PRO1907290008","PRO1909090002","PRO1906190006","PRO1908080013","PRO1908200006","PRO1906210020","PRO1908050002","PRO1908060001","PRO1907120005","PRO1906110001","PRO1909170001","PRO1911190001","PRO1906190003","PRO1910260002","PRO1906200001","PRO1910230001","PRO1906200004","PRO1907050005","PRO1911220006","PRO1907100006","PRO1907110007","PRO1904260002","PRO1908040001","PRO1907260010","PRO1906170007","PRO1909240004","PRO1906210043","PRO1908210002","PRO1907040006","PRO1911260001","PRO1906210021","PRO1906210018","PRO1907110013","PRO1908270005","PRO1908130008","PRO1910100002","PRO1910180001","PRO1907090016","PRO1906210065","PRO1911080002","PRO1906210006","PRO1908200004","PRO1906210079","PRO1906210042","PRO1906110004","PRO1904240038","PRO1906200005","PRO1907290006","PRO1911210001","PRO1911240003","PRO1906200006","PRO1908230001","PRO1907090011","PRO1909170002","PRO1909260003","PRO1910030002","PRO1910160006","PRO1909260004","PRO1907050006","PRO1906210015","PRO1910210001","PRO1906200002","PRO1908220007","PRO1909030008","PRO1907040008","PRO1911060001","PRO1909110002","PRO1907240004","PRO1910190001","PRO1906170002","PRO1907300001","PRO1907300007","PRO1909220001","PRO1907090004","PRO1906140008","PRO1910240002","PRO1906200003","PRO1907230005","PRO1906170003","PRO1910150001","PRO1911290001","PRO1907050002","PRO1911120002","PRO1907060001","PRO1908270008","PRO1911140017","PRO1910230002","PRO1912040004","PRO1908290006","PRO1907090015","PRO1907100004","PRO1909200003","PRO1904150001","PRO1905080001","PRO1908090002","PRO1909240002","PRO1906170006","PRO1909050003","PRO1908210003","PRO1909190002","PRO2004020002","PRO2004080003","PRO2010200002","PRO1905160006","PRO2002120001","PRO2004280003","PRO2006040001","PRO2010240004","PRO2001220001","PRO2005110004","PRO2007150002","PRO2011130004","PRO2002100001","PRO2004230005","PRO1908080012","PRO1906210066","PRO2002230001","PRO2003310001","PRO1908080019","PRO2007230003","PRO2005160001","PRO1911150006","PRO1909130003","PRO1909130004","PRO2003270001","PRO1907160005","PRO2004070001","PRO2001080003","PRO2003120003","PRO2005090001","PRO2006290001","PRO1910220002","PRO1912060002","PRO2005110002","PRO1911260003","PRO2003080002","PRO2001250002","PRO2005150001","PRO1906280003","PRO2001130001","PRO1903170001","PRO1911110001","PRO2001310001","PRO2001180001","PRO2006050004","PRO1909120006","PRO2007300003","PRO2005120001","PRO1910010004","PRO1912030003","PRO2006010001","PRO2002040002","PRO2003100005","PRO2004200001","PRO1902240001","PRO1906210078","PRO2007300008","PRO2008260001","PRO2004110002","PRO2005080002","PRO2003190002","PRO2001030001","PRO2004260004","PRO1905160002","PRO2005150003","PRO2001300004","PRO1907050004","PRO1912020004","PRO2004130003","PRO1906220001","PRO2002050001","PRO2011270003","PRO2008190003","PRO2003170003","PRO1907040003","PRO2008290003","PRO2003080001","PRO2008270004","PRO2006200004","PRO2006180002","PRO2012010002","PRO1906180006","PRO2009030004","PRO2007200005","PRO2007150001","PRO2006020001","PRO2006300009","PRO2006300008","PRO2002200001","PRO2009270002","PRO2004200003","PRO2003220002","PRO2004050001","PRO2003100004","PRO2007280003","PRO2006180007","PRO1909050004","PRO2007230005","PRO2004180001","PRO2004230001","PRO2005220003","PRO1906210072","PRO2004080001","PRO2004010005","PRO2007140001","PRO2005140002","PRO1910290002","PRO1912020003","PRO2009020005","PRO2007010005","PRO2002180001","PRO2007290004","PRO1911150003","PRO2008310003","PRO2003240001","PRO1911250002","PRO1904300005","PRO2003190001","PRO2004060002","PRO2010270003","PRO2007280004","PRO1907180001","PRO2002270001","PRO2006300003","PRO2003250001","PRO2010130008","PRO2003230003","PRO1910010005","PRO2006170001","PRO2003300001","PRO2005050004","PRO2007070004","PRO2010090005","PRO2005120003","PRO2009220002","PRO2006300010","PRO2006300011","PRO2004300001","PRO2012020003","PRO2005040002","PRO2007300009","PRO1904300008","PRO2004200002","PRO1911060002","PRO2004030001","PRO1912060001","PRO2004130002","PRO2003040001","PRO2010150004","PRO2004170002","PRO2008060002","PRO2003260002","PRO1911220005","PRO2002260002","PRO2004210002","PRO1912020001","PRO2004130001","PRO2007010001","PRO2004030004","PRO2006200001","PRO2002020001","PRO1906260002","PRO2004020003","PRO2009110002","PRO2007130002","PRO2002270004","PRO2007070001","PRO1908070003","PRO2001200001","PRO2002240001","PRO1909250002","PRO2004040001","PRO1910210002","PRO2007270004","PRO2004010003","PRO2006180005","PRO2002140001","PRO2008070003","PRO2008050005","PRO2011120002","PRO1910250001","PRO2012040001","PRO1907170002","PRO2006250006","PRO2006290005","PRO2005040001","PRO1911200007","PRO2010220004","PRO2003090002","PRO2009090010","PRO2006080001","PRO2011140003","PRO2009030007","PRO2006180008","PRO1910050002","PRO2002100002","PRO2002250003","PRO2007100001","PRO1911030001","PRO2001290004","PRO1911010001","PRO2004270004","PRO2005010001","PRO2006060002","PRO2004030003","PRO2006050005","PRO1911200008","PRO2002080001","PRO2003160001","PRO2004200008","PRO2005220002","PRO1912060006","PRO2004220003","PRO2004280007","PRO2008220002","PRO2004220004","PRO1911280001","PRO2010230004","PRO2001170001","PRO2008150004","PRO2008210002","PRO2004250002","PRO2007100002","PRO2004210004","PRO1911190003","PRO2007060003","PRO2006250001","PRO2009060002","PRO2009010006","PRO2007070002","PRO1912230002","PRO2003230002","PRO2006180001","PRO1909050002","PRO1912020002","PRO1910270002","PRO1910130001","PRO2010230003","PRO1912040002","PRO1908220003","PRO2009100007","PRO2008190002","PRO2003170005","PRO2007170004","PRO2007010002","PRO2008240002","PRO2006050002","PRO1906190009","PRO2001300007","PRO2006240001","PRO2011260001","PRO2006270002","PRO2006240003","PRO2002260001","PRO2004230002","PRO2011170004","PRO2004180002","PRO2007040001","PRO2008100003","PRO2002280001","PRO1906210087","PRO2009300001","PRO2003100001","PRO1911200003","PRO2006260003","PRO1912060007","PRO1907020006","PRO2003270002","PRO2007280006","PRO1911230001","PRO2004220001","PRO2001080002","PRO2006250004","PRO2006190001","PRO2004230004","PRO2007140002","PRO2007040002","PRO1908230002","PRO2006260004","PRO1912070003","PRO1912030002","PRO2010190003","PRO2005020001","PRO2001210001","PRO1911250001","PRO2004190003","PRO1911280002","PRO1910010001","PRO1907250024","PRO2004140001","PRO2003120004","PRO2004170003","PRO2003040003","PRO2003120005","PRO2005010002","PRO2007290002","PRO2004300002","PRO2001290002","PRO2008180002","PRO2005050001","PRO1910290004","PRO1909020003","PRO2003090003","PRO1901290001","PRO2009010007","PRO1908240002","PRO2006040002","PRO2004060003","PRO2006230003","PRO2004150003","PRO2009230001","PRO1909180002","PRO2005190001","PRO2006300004","PRO2004160002","PRO2009090007","PRO1912050002","PRO2004190002","PRO2004220005","PRO1909030001","PRO2004110001","PRO2007090001","PRO1908220010","PRO2007160003","PRO1911240001","PRO2008270005","PRO2001300001","PRO1910160007","PRO2008280001","PRO2004260002","PRO2003230004","PRO2010070008","PRO2004200005","PRO2003180001","PRO2003060001","PRO2003170002","PRO1909170003","PRO2003100003","PRO2005180001","PRO2005280001","PRO2009150004","PRO1909120004","PRO2006150002","PRO1908140001","PRO1908310001","PRO1909030007","PRO2003120001","PRO2004010002","PRO2001300006","PRO2006200003","PRO2005160002","PRO2006170002","PRO2009110007","PRO2004260001","PRO2004290004","PRO2002250002","PRO2004200007","PRO1911200006","PRO2007230001","PRO2007110001","PRO2007020001","PRO2004260003","PRO1911150002","PRO1907300003","PRO2005130001","PRO2007300004","PRO2004280002","PRO1911140014","PRO2005120005","PRO2007200002","PRO2004220002","PRO2009150003","PRO2009270003","PRO1908300002","PRO2004140002","PRO2008280003","PRO2005110003","PRO2008180001","PRO2005050003","PRO1908200013","PRO2001290001","PRO2006220003","PRO2011110004","PRO2010090002","PRO2002060001","PRO2005010004","PRO1910170002","PRO1912110002","PRO2007060001","PRO1907230013","PRO2011050001","PRO2005110001","PRO2001230002","PRO2011130003","PRO2002270003","PRO2005180002","PRO2009090001","PRO1908250001","PRO1910090003","PRO1912060004","PRO1911270002","PRO1912070002","PRO2008040004","PRO1912040005","PRO2005130003","PRO2002280002","PRO2009230005","PRO2007300005","PRO1911150005","PRO2004150002","PRO2011260010","PRO2002070001","PRO2006300007","PRO2002270005","PRO1910020002","PRO2004210001","PRO1910020005","PRO2001300002","PRO2008310001","PRO2002240002","PRO2009180001","PRO1909030003","PRO1909030004","PRO2008030002","PRO1910080003","PRO1910280005","PRO1911050006","PRO2007210003","PRO2003130001","PRO2004290002","PRO1910070005","PRO1907070001","PRO2004180003","PRO2005050002","PRO2006250002","PRO1912010001","PRO2006240007","PRO2004230003","PRO2011170001","PRO1910270001","PRO2004270003","PRO1911140011","PRO1912260001","PRO1912030005","PRO2005080001","PRO2001250001","PRO1909030006","PRO2005120004","PRO2005220001","PRO2002070002","PRO2009130003","PRO1911140003","PRO2006200005","PRO1909030002","PRO2004290005","PRO2005060001","PRO2004270002","PRO2008070001","PRO1910160004","PRO2004020001","PRO2004270001","PRO2001300003","PRO2003280001","PRO2001100001","PRO2004080002","PRO2009300004","PRO2006240004","PRO1909090001","PRO2009100004","PRO2009070001","PRO1908270001","PRO2007180002","PRO2004280005","PRO2004240001","PRO2003130002","PRO2010260005","PRO2003280002","PRO1910070004","PRO1907250019","PRO2007130004","PRO1910090002","PRO2007170001","PRO2008110003","PRO1911130001","PRO1911300003","PRO2006100001","PRO1908070004","PRO1912030001","PRO1910300004","PRO2011130005","PRO2009040004","PRO2006160001","PRO2006220001","PRO2009030001","PRO2009020003","PRO2011170002","PRO2006120001","PRO2007080001","PRO2011140001","PRO2003040002","PRO2006200002","PRO2011270005","PRO1911120001","PRO2010240003","PRO2003300002","PRO2012080001","PRO2101290002","PRO1905160003","PRO2011090003","PRO1909130002","PRO2101130002","PRO2102230001","PRO2104210001","PRO1911020003","PRO2007170005","PRO2010270004","PRO2010080001","PRO2006290003","PRO2105040002","PRO2007250001","PRO2004290001","PRO2104010002","PRO2008180003","PRO2011030002","PRO2103020001","PRO2004200006","PRO2107150001","PRO2102010003","PRO2010130006","PRO2012150002","PRO2007300006","PRO2009010003","PRO2104270002","PRO2103100001","PRO2006030001","PRO2104290001","PRO2007050001","PRO2011140002","PRO2008310002","PRO2101300001","PRO2012100001","PRO2010260002","PRO2008270006","PRO2012170002","PRO1910050001","PRO2006160002","PRO2005260001","PRO2103180002","PRO2010140001","PRO2004280006","PRO2012220003","PRO2009060003","PRO2010300004","PRO2011060001","PRO2012080004","PRO2008030001","PRO1908150015","PRO2011020008","PRO2009060001","PRO2008050004","PRO2101080005","PRO2012290003","PRO2108250001","PRO2010300001","PRO2007130005","PRO2103220001","PRO2004280004","PRO2106180002","PRO2009080004","PRO2005120002","PRO2011300008","PRO2006240006","PRO2009110001","PRO1908150016","PRO2008250003","PRO2108080002","PRO1907180006","PRO2101080006","PRO2011060002","PRO2010210001","PRO2002190003","PRO2010050004","PRO2106170006","PRO2105010003","PRO2102160001","PRO2103310002","PRO2009160002","PRO2011270001","PRO2101180003","PRO2011080001","PRO2012290005","PRO2008150003","PRO2103270003","PRO2109010003","PRO2009100003","PRO2104070002","PRO2106200002","PRO2010130003","PRO2003220001","PRO2011280003","PRO2102050001","PRO2108090001","PRO2011020003","PRO2011020003","PRO2003190003","PRO2009220003","PRO2107020002","PRO2010260006","PRO2109210001","PRO2106240002","PRO2005110005","PRO2012220001","PRO2101130001","PRO2010270010","PRO2009250003","PRO2010270006","PRO1907020003","PRO2003290001","PRO2010110002","PRO2008120002","PRO2012190001","PRO2006280001","PRO2108230001","PRO2004030005","PRO2012140001","PRO2007220003","PRO2101240001","PRO2011260006","PRO2103030003","PRO2010140003","PRO2008130002","PRO2009280005","PRO2006150001","PRO2007270001","PRO2012160001","PRO2104160002","PRO2101110002","PRO2009020006","PRO2012210004","PRO2010210003","PRO2012090002","PRO2010070004","PRO2012050003","PRO2103020004","PRO2010310005","PRO2010290001","PRO2012110001","PRO2011260007","PRO2007010003","PRO2101280001","PRO2010020001","PRO2012280003","PRO2011030008","PRO2009150001","PRO2109230004","PRO2108220001","PRO2012310003","PRO2102100002","PRO2104170002","PRO2102160002","PRO2105030001","PRO2104200001","PRO2011110002","PRO1907290002","PRO2012210001","PRO2105250001","PRO2109020002","PRO2012120002","PRO2007300007","PRO2101040001","PRO2005280002","PRO2009170007","PRO2103260002","PRO2106200004","PRO2007180001","PRO2101230003","PRO2101230004","PRO2011260004","PRO2009180002","PRO2101270006","PRO2106230001","PRO2003200001","PRO2007090002","PRO2012040002","PRO2008240001","PRO2011170005","PRO2010120002","PRO2106190001","PRO2009210005","PRO2103090001","PRO2104030001","PRO2009190002","PRO2009300006","PRO2009240003","PRO2010100002","PRO2009040002","PRO2009180005","PRO2009040001","PRO2103300003","PRO2009300005","PRO2009170003","PRO2010070007","PRO2008240004","PRO2010280002","PRO2007220001"])
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

        return view('tahap4.detailpekebun')->with(compact('id_legalitas','data_pekebun_selected','get_data_legalitas_pekebun_selected','get_data_kelembagaan_pekebun','data_pekebun_sudah_rekon','legalitas_lahan_pekebun_proposal_lain','legalitas_lahan_pekebun_proposal','popup_legalitas_lahan_pekebun_proposal','id_proposal'));
    }

    public function detail4pekebunsave(Request $request,$id_proposal, $id_legalitas_lahan_pekebun)
    {
        try{
            $request->validate([
                'nama_tertera_dokumen_legalitas_lahan' => 'required',
                'nomor_dokumen_legalitas_lahan' => 'required',
                'jenis_dokumen_legalitas_lahan' => 'required',
                'tanggal_terbit_dokumen_legalitas_lahan' => 'required',
                'file_dokumen_legalitas_lahan' => 'nullable|mimes:pdf|max:30720',
                'file_shm_beda_nama' => 'nullable|mimes:pdf|max:30720',
            ],[
                'nama_tertera_dokumen_legalitas_lahan.required' => 'Nama Dokumen Legalitas Lahan harus diisi',
                'nomor_dokumen_legalitas_lahan.required' => 'Nomor Dokumen Legalitas Lahan harus diisi',
                'jenis_dokumen_legalitas_lahan.required' => 'Jenis Dokumen Legalitas Lahan harus diisi',
                'tanggal_terbit_dokumen_legalitas_lahan.required' => 'Tanggal Terbit Dokumen Legalitas Lahan harus diisi',
                'file_dokumen_legalitas_lahan.mimes' => 'Jenis file Dokumen Legalitas Lahan harus PDF',
                'file_dokumen_legalitas_lahan.max' => 'Ukuran file Dokumen Legalitas Lahan maksimal 30 MB',
                'file_shm_beda_nama.mimes' => 'Jenis file SHM Beda Nama harus PDF',
                'file_shm_beda_nama.max' => 'Ukuran file SHM Beda Nama maksimal 30 MB',
            ]);


            $longitude = explode(',',$request->longitude);
            $latitude = explode(',',$request->latitude);
            $jumlah_longitude = count(array_filter($longitude));
            $jumlah_latitude = count(array_filter($latitude));
            if($jumlah_longitude < 4 || $jumlah_latitude < 4 || $jumlah_longitude != $jumlah_latitude){
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Koordinat lahan harus diisi (minimal 4 pasang koordinat)',
                ], 500);
            }


            DB::beginTransaction();
                $legalitas_lahan_pekebun = DB::connection('mysql_rdp')
                ->table('legalitas_lahan_pekebun')
                ->where('id','=',$id_legalitas_lahan_pekebun)
                ->first();

                $proposal = DB::connection('mysql_rdp')
                    ->table('proposal')
                    ->where('id',$legalitas_lahan_pekebun->id_proposal)
                    ->select('nomor_proposal')
                    ->first();
                $pekebun = DB::connection('mysql_rdp')
                    ->table('pekebun')
                    ->where('id',$legalitas_lahan_pekebun->id_pekebun)
                    ->first();

                $luas_lahan_pernah_diajukan = DB::connection('mysql_rdp')
                    ->table('legalitas_lahan_pekebun')
                    ->where('id_pekebun',$legalitas_lahan_pekebun->id_pekebun)
                    ->whereNull('deleted_at')
                    ->select(
                        DB::raw('SUM(luas_polygon_legalitas_lahan) AS luas_lahan_pernah_diajukan')
                    )->first()->luas_lahan_pernah_diajukan;

                $id_proposal = $legalitas_lahan_pekebun->id_proposal;
                $id_pekebun = $legalitas_lahan_pekebun->id_pekebun;
                $nama_tertera_dokumen_legalitas_lahan = $request->nama_tertera_dokumen_legalitas_lahan;
                $nomor_dokumen_legalitas_lahan = $request->nomor_dokumen_legalitas_lahan;
                $jenis_dokumen_legalitas_lahan = $request->jenis_dokumen_legalitas_lahan;
                $tanggal_terbit_dokumen_legalitas_lahan = $request->tanggal_terbit_dokumen_legalitas_lahan;
                $hitungan_luas_lahan = floatval($request->hitungan_luas_lahan);
                $date = date('Y-m-d H:i:s');
                $username = Auth::user()->email;

                $polygon_legalitas_lahan = json_encode(
                    array_map(function($longitude, $latitude) {
                        return [$longitude,$latitude];
                    }, $longitude, $latitude)
                );

                $update_legalitas_lahan_pekebun = [
                    'nomor_dokumen_legalitas_lahan' => $nomor_dokumen_legalitas_lahan,
                    'nama_tertera_dokumen_legalitas_lahan' => $nama_tertera_dokumen_legalitas_lahan,
                    'jenis_dokumen_legalitas_lahan' => $jenis_dokumen_legalitas_lahan,
                    'tanggal_terbit_dokumen_legalitas_lahan' => $tanggal_terbit_dokumen_legalitas_lahan,
                    'polygon_legalitas_lahan' => $polygon_legalitas_lahan,
                    'updated_at' => $date,
                    'updated_by' => $username,
                ];

                if($request->hasFile('file_dokumen_legalitas_lahan')){
                    $file_dokumen_legalitas_lahan = $request->file('file_dokumen_legalitas_lahan');

                    $nama_asli_dokumen = $file_dokumen_legalitas_lahan->getClientOriginalName();
                    $folder_dokumen = 'dokumen_pekebun/'.$pekebun->nik_pekebun.'/dokumen_legalitas_lahan';
                    $extension_dokumen = $file_dokumen_legalitas_lahan->getClientOriginalExtension();
                    $nama_dokumen_disimpan = strtoupper(date("YmdHis").'_dokumen_legalitas_lahan_'.$proposal->nomor_proposal.'_'.$pekebun->nik_pekebun).'.'.$extension_dokumen;
                    $path_dokumen = $file_dokumen_legalitas_lahan->storeAs($folder_dokumen, $nama_dokumen_disimpan);

                    $update_legalitas_lahan_pekebun['nama_asli_file_dokumen_legalitas_lahan'] = $nama_asli_dokumen;
                    $update_legalitas_lahan_pekebun['nama_file_dokumen_legalitas_lahan'] = $nama_dokumen_disimpan;
                    $update_legalitas_lahan_pekebun['lokasi_file_dokumen_legalitas_lahan'] = $path_dokumen;
                }
                if($request->jenis_dokumen_legalitas_lahan == 'SHM' && $request->hasFile('file_shm_beda_nama')){
                    $file = $request->file('file_shm_beda_nama');

                    $nama_asli_dokumen = $file->getClientOriginalName();
                    $folder_dokumen = 'dokumen_pekebun/'.$pekebun->nik_pekebun.'/dokumen_legalitas_lahan';
                    $extension_dokumen = $file->getClientOriginalExtension();
                    $nama_dokumen_disimpan = strtoupper(date("YmdHis").'_shm_beda_nama_'.$proposal->nomor_proposal.'_'.$pekebun->nik_pekebun).'.'.$extension_dokumen;
                    $path_dokumen = $file->storeAs($folder_dokumen, $nama_dokumen_disimpan);

                    $update_legalitas_lahan_pekebun['nama_asli_file_shm_beda_nama'] = $nama_asli_dokumen;
                    $update_legalitas_lahan_pekebun['nama_file_shm_beda_nama'] = $nama_dokumen_disimpan;
                    $update_legalitas_lahan_pekebun['lokasi_file_shm_beda_nama'] = $path_dokumen;
                }elseif($request->jenis_dokumen_legalitas_lahan != 'SHM'){
                    $update_legalitas_lahan_pekebun['nama_asli_file_shm_beda_nama'] = null;
                    $update_legalitas_lahan_pekebun['nama_file_shm_beda_nama'] = null;
                    $update_legalitas_lahan_pekebun['lokasi_file_shm_beda_nama'] = null;
                }

                $legalitas_lahan_pekebun_history = collect($legalitas_lahan_pekebun)->toArray();
                unset($legalitas_lahan_pekebun_history['id']);
                $legalitas_lahan_pekebun_history['id_legalitas_lahan_pekebun'] = $id_legalitas_lahan_pekebun;
                $legalitas_lahan_pekebun_history['kolom_yang_berubah'] = implode(',',array_keys($update_legalitas_lahan_pekebun));
                $legalitas_lahan_pekebun_history['created_at'] = $date;
                $legalitas_lahan_pekebun_history['created_by'] = $username;
                $legalitas_lahan_pekebun_history['updated_at'] = $date;
                $legalitas_lahan_pekebun_history['updated_by'] = $username;

                $legalitas_lahan_pekebun_log = [
                    'id_legalitas_lahan_pekebun' => $id_legalitas_lahan_pekebun,
                    'id_proposal' => $id_proposal,
                    'log' => 'Legalitas Lahan Pekebun '.$pekebun->nama_pekebun.' (NIK: '.$pekebun->nik_pekebun.') untuk Proposal '.$proposal->nomor_proposal.' diperbarui oleh User '.$username,
                    'created_at' => $date,
                    'created_by' => $username,
                ];

                $insert_pekebun_history = collect($pekebun)->toArray();
                unset($insert_pekebun_history['id']);
                $insert_pekebun_history['id_pekebun'] = $id_pekebun;
                $insert_pekebun_history['created_at'] = $date;
                $insert_pekebun_history['created_by'] = $username;
                $insert_pekebun_history['updated_at'] = $date;
                $insert_pekebun_history['updated_by'] = $username;

                $insert_pekebun_log = [
                    'id_pekebun' => $id_pekebun,
                    'log' => 'Legalitas Lahan '.$pekebun->nama_pekebun.' (NIK: '.$pekebun->nik_pekebun.') sesuai Proposal '.$proposal->nomor_proposal.' diperbarui oleh User '.$username,
                    'created_at' => $date,
                    'created_by' => $username,
                ];

                DB::connection('mysql_rdp')
                    ->table('legalitas_lahan_pekebun')
                    ->where('id','=',$id_legalitas_lahan_pekebun)
                    ->update($update_legalitas_lahan_pekebun);
                DB::connection('mysql_rdp')
                    ->table('legalitas_lahan_pekebun_history')
                    ->insert($legalitas_lahan_pekebun_history);
                DB::connection('mysql_rdp')
                    ->table('legalitas_lahan_pekebun_log')
                    ->insert($legalitas_lahan_pekebun_log);
                DB::connection('mysql_rdp')
                    ->table('pekebun_history')
                    ->insert($insert_pekebun_history);
                DB::connection('mysql_rdp')
                    ->table('pekebun_log')
                    ->insert($insert_pekebun_log);
            DB::commit();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Informasi lahan pekebun berhasil diperbarui.',
            ], 200);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 'Error',
                'errors' => $e->errors(),
                'message' => 'Informasi lahan pekebun tidak lengkap',
            ], 500);
        }catch(QueryException $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan dalam menyimpan informasi lahan pekebun.',
            ], 500);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function auto_rekon_tahap_1(Request $request) {
        $now = date('Y-m-d H:i:s');

        $get_list_proposal_rekon = DB::connection('mysql_rdp')
                                        ->table('lookup_proposal_kelembagaan_pekebun')
                                        ->whereNotNull('id_proposal_psr_online')
                                        ->whereNotNull('id_proposal_smart_psr')
                                        ->get();

        $list_proposal_psr_online = $get_list_proposal_rekon->pluck('id_proposal_psr_online');
        $list_proposal_smart_psr = $get_list_proposal_rekon->pluck('id_proposal_smart_psr');

        $get_data_pekebun_psr_online =  DB::connection('mysql_psr')
                                        ->table('tb_pekebun')
                                        ->whereIn('id_proposal',$list_proposal_psr_online)
                                        ->select(
                                            'id_pekebun',
                                            'id_proposal',
                                            'id_koperasi',
                                            'no_kk',
                                            'no_ktp',
                                            'nama_pekebun',
                                            'tgl_lahir',
                                            'alamat_pekebun',
                                            'provinsi',
                                            'kabupaten',
                                            'kecamatan',
                                            'kelurahan',
                                            'kodepos',
                                            'hp_pekebun'
                                            )
                                        ->get();

        foreach ($get_data_pekebun_psr_online as $key => $value) {
            $id_proposal_smart_psr_lookup = $get_list_proposal_rekon->where('id_proposal_psr_online','=',$value->id_proposal)->value('id');
            $get_data_pekebun_psr_online[$key]->id_proposal_smart_psr = $id_proposal_smart_psr_lookup;
        }

        // dd($get_data_pekebun_psr_online);

        $get_data_pekebun_smart_psr =   DB::connection('mysql_smart_psr')
                                        ->table('tbl_master_pekebun')
                                        ->whereIn('id_proposal',$list_proposal_smart_psr)
                                        ->select('id','id_proposal','luas_hektar','nik')
                                        ->get();

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
                    'log' => "Legalitas Pekebun ".strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun))." (NIK: ".trim($get_data_pekebun_psr_online->no_ktp).") di migrasi oleh User RDP - ".Auth::user()->name." melalui aplikasi migrasi RDP",
                    'created_at' => $now,
                    'created_by' => "RDP - ".Auth::user()->name
                ]);
            }

        return redirect()->back();

    }

    public function rekap_proposal($id_proposal){

        $data_lp = DB::connection('mysql_rdp')
                    ->table('proposal')
                    ->join('kelembagaan_pekebun','kelembagaan_pekebun.id','=','proposal.id_kelembagaan_pekebun')
                    ->where('proposal.id',$id_proposal)
                    ->select('nomor_proposal','nama_kelembagaan_pekebun')
                    ->first();

        $id_pekebun = DB::connection('mysql_rdp')
                        ->table('pekebunnya_proposal')
                        ->where('id_proposal',$id_proposal)
                        ->pluck('id_pekebun');

        $get_nik_pekebun = DB::connection('mysql_rdp')
                            ->table('pekebun')
                            ->whereIn('id',$id_pekebun)
                            ->pluck('nik_pekebun');

        $data_pekebun_psr_online = DB::connection('mysql_psr')
                                    ->table('tb_pekebun')
                                    ->whereIn('no_ktp',$get_nik_pekebun)
                                    ->orderBy('id_proposal','ASC')
                                    ->select('nama_pekebun','no_ktp','surat_kuasa','fc_kk','fc_ktp','surat_stdb','surat_kuasa','fc_buku_tabungan')
                                    ->get();
        $array_pekebun = [];
        $i = 1;
        foreach ($data_pekebun_psr_online as $key => $value) {
            $array_pekebun[] = [
                $i++,
                $value->nama_pekebun,
                '="'.$value->no_ktp.'"',
                (empty(trim($value->surat_kuasa)) == false ? "Ada" : "Tidak Ada"),
                (empty(trim($value->fc_ktp)) == false ? "Ada" : "Tidak Ada"),
                (empty(trim($value->fc_kk)) == false ? "Ada" : "Tidak Ada"),
                (empty(trim($value->surat_stdb)) == false ? "Ada" : "Tidak Ada"),
                (empty(trim($value->surat_kuasa)) == false ? "Ada" : "Tidak Ada"),
                (empty(trim($value->fc_buku_tabungan)) == false ? "Ada" : "Tidak Ada"),

            ];
        }

        // dd($array_pekebun);

        $excel = Excel::create(['Sheet1']);
        $sheet = $excel->sheet();
        $sheet->setColWidths(
            [
                5,17,17,17,17,17,17,17,17
            ]
        );
        // Write heads

        // Write data
        $colFormats = [
            '0',
            '@',
            '@',
            '@',
            '@',
            '@',
            '@',
            '@',
            '@',
        ];
        $sheet->setColFormats($colFormats);
        $sheet->writeTo('A1','Nomor Proposal');
        $sheet->writeTo('B1',$data_lp->nomor_proposal);
        $sheet->writeTo('A2','Nama Kelembagaan Pekebun');
        $sheet->writeTo('B2',$data_lp->nama_kelembagaan_pekebun);
        $sheet->writeTo('A3','');
        $sheet->writeRow(['No', 'Nama Pekebun', 'NIK','Scan KTP','Scan KK','Scan Surat Kuasa','Scan STDB (2018 Kebawah)','Scan Surat Kuasa','Scan Buku Tabungan']);

        foreach($array_pekebun as $rowData) {
            $sheet->writeRow($rowData);
        }
        $filename = 'Rekap Data Pekebun Proposal '.$data_lp->nomor_proposal.' Per '.date('Y_m_d H_i_s').'.xlsx';
        $excel->save($filename);

        return Response::download($filename)->deleteFileAfterSend(true);

    }

    public function rekap_kelembagaan_pekebun($tahun_rekomtek){
        $get_id_proposal_psr_online = DB::connection('mysql_psr')
                                    ->table('tb_proposal')
                                    ->whereYear('tgl_terbit_rekomendasi_ditjenbun','=',$tahun_rekomtek)
                                    ->orderBy('id_proposal','ASC')
                                    ->select('id_proposal','id_koperasi')
                                    ->get();

        $data_koperasi = array();
        $i = 1;
        foreach ($get_id_proposal_psr_online as $key => $value) {
            $dokumen_persyaratan = DB::connection('mysql_psr')
                                    ->table('tb_proposal')
                                    ->where('id_proposal','=',$value->id_proposal)
                                    ->select("no_dokumen","surat_permohonan","surat_stdb","metode_peremajaan","bibit_unggul","peta_lokasi_kebun","rab","offering_letter","offtaker","legalitas_koperasi","bebas_kawasan","hasil_rekomendasi_kabupaten","hasil_rekomendasi_provinsi","cpcl_bupati","hasil_rekomendasi_ditjenbun","perjanjian","sk_penetapan_dirut","surat_bap","pernyataan_mutlak","bukti_spm","kwitansi_pembayaran","kerjasama_kerja","daftar_rekening_pekebun","profil_lahan","produktivitas","profil_pekebun","rat","keterangan_legalitas_lahan","berita_acara_kab","kawasan_hutan_klhk","berita_acara_prov","kemitraan_kerja_usaha","permintaan_penetapan_cpcl","kawasan_hutan_bupati","cpcl_nominatif","dokumen_bank","hasil_verifikasi_ditjenbun","sk_penunjukan","berita_acara_bpdpks","file_rab","sp_peremajaan","sk_benih_sawit","sk_legalitas_bpn","sp_kebun","sk_gambut","rencana_kerja","shp_file","surveyor_surat_tugas","surveyor_laporan")
                                    ->first();
            $data_koperasi[$value->id_proposal] = array();
            array_push($data_koperasi[$value->id_proposal],$i);

            foreach ($dokumen_persyaratan as $nama_file => $dokumen) {
                // array_push($data_koperasi[$value->id_proposal],"asd");
                if($nama_file == "no_dokumen"){
                    array_push($data_koperasi[$value->id_proposal],$dokumen);
                }else{
                    array_push($data_koperasi[$value->id_proposal],(empty(trim($dokumen)) == false ? "Ada" : "Tidak Ada"));
                }
            }
            $i++;
        }

        $excel = Excel::create(['Sheet1']);
        $sheet = $excel->sheet();
        // $sheet->setColWidths(
        //     [
        //         5,75,17,36,38,38
        //     ]
        // );
        // Write heads

        // Write data
        $colFormats = [
            '0',
            '@',
            '@',
            '@',
            '@',
            '@'
        ];
        $sheet->setColFormats($colFormats);
        $sheet->writeTo('A1','Tahun Rekomtek');
        $sheet->writeTo('B1',$tahun_rekomtek);
        $sheet->writeTo('A2','');
        $sheet->writeRow(["Nomor","Nomor Proposal","surat_permohonan","surat_stdb","metode_peremajaan","bibit_unggul","peta_lokasi_kebun","rab","offering_letter","offtaker","legalitas_koperasi","bebas_kawasan","hasil_rekomendasi_kabupaten","hasil_rekomendasi_provinsi","cpcl_bupati","hasil_rekomendasi_ditjenbun","perjanjian","sk_penetapan_dirut","surat_bap","pernyataan_mutlak","bukti_spm","kwitansi_pembayaran","kerjasama_kerja","daftar_rekening_pekebun","profil_lahan","produktivitas","profil_pekebun","rat","keterangan_legalitas_lahan","berita_acara_kab","kawasan_hutan_klhk","berita_acara_prov","kemitraan_kerja_usaha","permintaan_penetapan_cpcl","kawasan_hutan_bupati","cpcl_nominatif","dokumen_bank","hasil_verifikasi_ditjenbun","sk_penunjukan","berita_acara_bpdpks","file_rab","sp_peremajaan","sk_benih_sawit","sk_legalitas_bpn","sp_kebun","sk_gambut","rencana_kerja","shp_file","surveyor_surat_tugas","surveyor_laporan"]);

        foreach($data_koperasi as $rowData) {
            $sheet->writeRow($rowData);
        }
        $filename = 'Rekap Dokumen Rekomtek Tahun '.$tahun_rekomtek.' Per '.date('Y_m_d H_i_s').'.xlsx';
        $excel->save($filename);

        return Response::download($filename)->deleteFileAfterSend(true);

    }

}
