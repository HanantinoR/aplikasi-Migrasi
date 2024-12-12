<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use DB;
use Response;
use \avadim\FastExcelWriter\Excel;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('manajemen_akun.index', compact('users'));
    }

    public function pendaftaran_akun()
    {
        $users = User::all();
        return view('manajemen_akun.pendaftaran_akun', compact('users'));
    }

    public function post_daftar_unit_kerja_dinas(Request $request){
        $jabatan = $request->jabatan;

        $unit_kerja_dinas = DB::connection('mysql_rdp')
                            ->table('acuan_dinas_perkebunan')
                            ->where('tingkat','=',$jabatan)
                            ->get();

        return compact('unit_kerja_dinas');
    }

    public function post_pendaftaran_akun(Request $request){
        try{
            $request->validate([
                'nama_pengguna' => 'required',
                'jabatan' => 'required',
                'unit_kerja' => 'required',
                'email_pengguna' => 'required|email|unique:users,email',
                'password_pengguna' => 'required',
            ],[
                'nama_pengguna.required' => 'Nama Pengguna Harus Diisi',
                'jabatan.required' => 'Jabatan Harus Dipilih',
                'unit_kerja.required' => 'Unit Kerja Harus Dipilih',
                'email_pengguna.required' => 'Email Harus Diisi',
                'email_pengguna.email' => 'Format Email Salah',
                'email_pengguna.unique' => 'Email Sudah Digunakan',
                'password_pengguna.required' => 'Password Harus Diisi',
            ]);
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->nama_pengguna,
                'email' => $request->email_pengguna,
                'password' => Hash::make($request->password_pengguna),
                'roles' => $request->jabatan,
                'location' => $request->unit_kerja,
            ]);
            DB::commit();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Pengguna baru berhasil dibuat.',
            ], 200);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 'Error',
                'errors' => $e->errors(),
                'message' => 'Informasi pengguna tidak lengkap',
            ], 500);
        }catch(QueryException $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan dalam menyimpan informasi pengguna.',
            ], 500);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function edit_akun(Request $request, $id_pengguna)
    {
        $users = DB::table('users')->where('id','=',$id_pengguna)->first();
        return view('manajemen_akun.edit_akun', compact('users'));
    }

    public function post_edit_akun(Request $request, $id_pengguna){
        try{
            $request->validate([
                'nama_pengguna' => 'required',
                'jabatan' => 'required',
                'unit_kerja' => 'required',
                'email_pengguna' => 'required|email',
            ],[
                'nama_pengguna.required' => 'Nama Pengguna Harus Diisi',
                'jabatan.required' => 'Jabatan Harus Dipilih',
                'unit_kerja.required' => 'Unit Kerja Harus Dipilih',
                'email_pengguna.required' => 'Email Harus Diisi',
                'email_pengguna.email' => 'Format Email Salah',
            ]);


            DB::beginTransaction();
            $cek_email_ada_apa_engga = DB::table('users')->where('email','=',$request->email_pengguna)->where('id','!=',$id_pengguna)->count();

            if ($cek_email_ada_apa_engga >= 1) {
                $user = DB::table('users')
                ->where('id','=',$id_pengguna)
                ->update([
                    'name' => $request->nama_pengguna,
                    'roles' => $request->jabatan,
                    'location' => $request->unit_kerja,
                ]);
            }else{
                $user = DB::table('users')
                ->where('id','=',$id_pengguna)
                ->update([
                    'name' => $request->nama_pengguna,
                    'email' => $request->email_pengguna,
                    'roles' => $request->jabatan,
                    'location' => $request->unit_kerja,
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Pengguna berhasil di ubah.',
            ], 200);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 'Error',
                'errors' => $e->errors(),
                'message' => 'Informasi pengguna tidak lengkap',
            ], 500);
        }catch(QueryException $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan dalam menyimpan informasi pengguna.',
            ], 500);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function edit_password(Request $request, $id_pengguna)
    {
        $users = DB::table('users')->where('id','=',$id_pengguna)->first();
        return view('manajemen_akun.ubah_password', compact('users'));
    }

    public function post_edit_password(Request $request, $id_pengguna){
        try{
            $request->validate([
                'password_pengguna' => 'required',
            ],[
                'password_pengguna.required' => 'Password Harus Diisi',
            ]);


            DB::beginTransaction();

            $user = DB::table('users')
            ->where('id','=',$id_pengguna)
            ->update([
                'password' => Hash::make($request->password_pengguna),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Passowrd Pengguna berhasil di ubah.',
            ], 200);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 'Error',
                'errors' => $e->errors(),
                'message' => 'Informasi pengguna tidak lengkap',
            ], 500);
        }catch(QueryException $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Terjadi kesalahan dalam menyimpan informasi pengguna.',
            ], 500);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'Error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }


}
