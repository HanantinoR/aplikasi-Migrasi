<x-app-layout>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />
        <div class="container-fluid py-4 px-5">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="d-md-flex align-items-center mb-3 mx-2">
                        @php
                            $daerah = DB::connection('mysql_rdp')
                                        ->table('acuan_dinas_perkebunan')
                                        ->where('id','=',Auth::user()->location)
                                        ->value('nama_lengkap_dinas');
                        @endphp
                        <div class="mb-md-0 mb-3">
                            <h3 class="font-weight-bold mb-0">Hello, {{ Auth::user()->name }} - {{@$daerah}}</h3>
                            <p class="mb-0">Semangat Rekonnyaaaa!</p>
                        </div>
                    </div>                </div>
            </div>
            <hr class="my-0">
            <div class="row mt-4">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-xs mb-4">
                        <div class="card-header border-bottom pb-0">
                            <div class="d-sm-flex align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-semibold text-lg mb-0">Manajemen Akun</h6>
                                    <p class="text-sm mb-sm-0"><strong>List Akun Aplikasi Rekon Data PSR</strong></p>
                                </div>
                                <div class="ms-auto d-flex">
                                    {{-- <div class="input-group input-group-sm ms-auto me-2">
                                        <span class="input-group-text text-body">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z">
                                                </path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control form-control-sm"
                                            placeholder="Search">
                                    </div> --}}
                                    <a href="{{route('manajemen_akun.pendaftaran_akun')}}" type="button" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 me-2">
                                        <span class="btn-inner--icon">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="d-block me-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </span>
                                        <span class="btn-inner--text">Tambah Akun</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center justify-content-center mb-0" id="tableTahap1">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                No
                                            </th>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                Nama Akun
                                            </th>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                E-mail Akun
                                            </th>

                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                Tingkat
                                            </th>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                Lokasi
                                            </th>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                Log Singkat Akun
                                            </th>
                                            <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ( $users as $user )
                                            <tr>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$no++}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$user->name}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$user->email}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$user->roles}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$user->location}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    {{$user->created_at}}
                                                </th>
                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                    <a href='{{route('manajemen_akun.edit_akun',["id_pengguna"=>$user->id])}}' type="button" class="btn btn-sm btn-warning m-0 ">Edit Akun!</a>
                                                    <a href='{{route('manajemen_akun.edit_password',["id_pengguna"=>$user->id])}}' type="button" class="btn btn-sm btn-warning m-0 ">Ubah Password!</a>
                                                </th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-app-layout>
<script>
    $(document).ready(function(){
            $('#tableTahap1').DataTable({

            });
    });
</script>