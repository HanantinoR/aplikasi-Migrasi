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
                                        ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi')
                                        ->orderBy('tb_proposal.id_proposal','ASC')
                                        ->first();

        $get_id_hasil_rekon =   DB::connection('mysql_rdp')
                                ->table('lookup_pekebun_proposal')
                                ->where('id_proposal_psr_online','=',$id_proposal)
                                ->where('id_proposal_smart_psr','=',$get_id_proposal_smart_psr)
                                ->get();

        // Key By Data Pekebun buat di Apus Kalo Dia Udah di Rekon
        $data_pekebun_psr_online = $data_pekebun_psr_online->keyBy('id_pekebun');
        $data_pekebun_smart_psr = $data_pekebun_smart_psr->keyBy('id');
        // dd($get_id_hasil_rekon);
        foreach ($get_id_hasil_rekon as $key => $value) {
            unset($data_pekebun_psr_online[$value->id_pekebun_psr_online]);
            unset($data_pekebun_smart_psr[$value->id_pekebun_smart_psr]);
        }

        return view('tahap1.detail')->with(compact('data_pekebun_psr_online','data_pekebun_smart_psr','get_data_kelembagaan_pekebun'));
    }

    public function post_rekon_tahap_1(Request $request) {
        // dd($request->all());
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
                                            ->count();

        // Cek Udah Ke Daftar Belom Proposal Sama LP Nya
        $cek_terdaftar_lembaga_pekebun =    DB::connection('mysql_rdp')
                                            ->table('kelembagaan_pekebun')
                                            ->where('nama_kelembagaan_pekebun','=',trim($get_data_kelembagaan_pekebun_psr_online->koperasi))
                                            ->first();


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

        // $cek_proposal_rdp =  DB::connection('mysql_rdp')
        //                                     ->table('proposal')
        //                                     ->where('nomo_proposal','=',trim($nomor_proposal))
        //                                     ->first();

        // if ($cek_proposal_rdp === null) {
        //     $id_nomor_proposal = DB::connection('mysql_rdp')
        //                          ->table('proposal')
        //                          ->insertGetId([
        //                             "id_kelembagaan_pekebun" => $id_kelembagaan_pekebun,
        //                             "nomor_proposal" => $nomor_proposal,
        //                             "tanggal_pengajuan_proposal" => ,
        //                             "jalur_pengajuan" => ,
        //                             "status_lahan" => ,
        //                             "bank_mitra" => ,
        //                             "cabang_bank_mitra" => ,
        //                             "rencana_anggaran_belanja" => ,
        //                             "produktifitas_tanaman" => ,
        //                             "tahun_tanam_tanaman" => ,
        //                             "luas_lahan_diajukan" => ,
        //                             "luas_lahan_didanai" => ,
        //                             "luas_lahan_dikembalikan" => ,
        //                             "status_verifikasi_dokumen" => ,
        //                             "tanggal_verifikasi_dokumen" => ,
        //                             "verifikator_verifikasi_dokumen" => ,
        //                             "keterangan_verifikasi_dokumen" => ,
        //                             "created_at" => ,
        //                             "created_by" => ,
        //                             "updated_at" => ,
        //                             "updated_by" => ,
        //                             "step" => ,
        //                             "status_push_verifikator" => ,
        //                          ]);
        // }



        // dd($nomor_proposal,$request->all(),$cek_nik_udah_kedaftar_apa_belom,$get_data_pekebun_psr_online->tgl_lahir);
            // Insert Legalitas Dia
            $get_data_legalitas =   DB::connection('mysql_psr')
                                    ->table('tb_legalitas')
                                    ->where('id_pekebun','=',$request->id_pekebun_psr_online)
                                    ->get();

            // foreach ($get_data_legalitas as $key => $value) {
            //     $id_legalitas = DB::connection('mysql_rdp')
            //                     ->table('legalitas_lahan_pekebun')
            //                     ->insert([
            //                         "id_proposal" => ,
            //                         "id_pekebun" => ,
            //                         "jenis_dokumen_legalitas_lahan" => ,
            //                         "nomor_dokumen_legalitas_lahan" => ,
            //                         "nama_tertera_dokumen_legalitas_lahan" => ,
            //                         "tanggal_terbit_dokumen_legalitas_lahan" => ,
            //                         "nama_asli_file_dokumen_legalitas_lahan" => ,
            //                         "nama_file_dokumen_legalitas_lahan" => ,
            //                         "lokasi_file_dokumen_legalitas_lahan" => ,
            //                         "polygon_legalitas_lahan" => ,
            //                         "luas_polygon_legalitas_lahan" => ,
            //                         "polygon_verifikasi_legalitas_lahan" => ,
            //                         "luas_polygon_verifikasi_legalitas_lahan" => ,
            //                         "verifikator_verifikasi_legalitas_lahan" => ,
            //                         "keterangan_verifikasi_legalitas_lahan" => ,
            //                         "created_at" => ,
            //                         "created_by" => ,
            //                         "updated_at" => ,
            //                         "updated_by" => ,
            //                         "deleted_at" => ,
            //                         "deleted_by" => ,
            //                     ])
            // }

            dd($get_data_legalitas);

        if ($cek_nik_udah_kedaftar_apa_belom >= 1) {

        }else{
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
                'created_by' => Auth::user()->name
            ]);

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

            // Insert Log Pekebun Di Migrasiin
            DB::connection('mysql_rdp')
            ->table('pekebun_log')
            ->insert([
                'id_pekebun' => $id_pekebun,
                'log' => "Pekebun ".strtoupper(trim($get_data_pekebun_psr_online->nama_pekebun) == null ? NULL : trim($get_data_pekebun_psr_online->nama_pekebun))." (NIK: ".trim($get_data_pekebun_psr_online->no_ktp) == null ? NULL : trim($get_data_pekebun_psr_online->no_ktp).") di migrasi oleh User RDP - ".Auth::user()->email." melalui aplikasi migrasi RDP",
                'created_at' => $now,
                'created_by' => "RDP - ".Auth::user()->email
            ]);


        }

        dd($nomor_proposal,$request->all(),$cek_nik_udah_kedaftar_apa_belom,$get_data_pekebun_psr_online);

    }

    public function index2()
    {
        $get_id_proposal = DB::connection('mysql_rdp')
        ->table('lookup_proposal_kelembagaan_pekebun')
        ->get();

        $list_id_proposal_psr_online = $get_id_proposal->pluck('id_proposal_psr_online');

        $get_data_kelembagaan_pekebun = DB::connection('mysql_psr')
                            ->table('tb_proposal')
                            ->whereIn('tb_proposal.id_proposal',$list_id_proposal_psr_online)
                            ->join('tb_koperasi','tb_koperasi.id_koperasi','=','tb_proposal.id_koperasi')
                            ->select('tb_proposal.id_proposal','tb_proposal.no_dokumen','tb_koperasi.koperasi')
                            ->orderBy('tb_proposal.id_proposal','ASC')
                            ->get();
        return view('tahap2.index')->with(compact('get_data_kelembagaan_pekebun'));
    }

    public function detail2()
    {
        return view('tahap2.detail');
    }

    public function index3()
    {
        return view('tahap3.index');
    }

    public function detail3()
    {
        return view('tahap3.detail');
    }

    public function index4()
    {
        return view('tahap4.index');
    }

    public function detail4()
    {
        return view('tahap4.detail');
    }
}
