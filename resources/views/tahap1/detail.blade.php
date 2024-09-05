<x-app-layout>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />
        <div class="container-fluid py-4 px-5">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="d-md-flex align-items-center mb-3 mx-2">
                        <div class="mb-md-0 mb-3">
                            <h3 class="font-weight-bold mb-0">Halooo, {{ Auth::user()->name }}</h3>
                            <p class="mb-0">Semangat Rekon Datanyaaaaa!</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="row mt-4">
                <div class="col-lg-12 col-md-12">
                    <div class="card  shadow-xs mb-4">
                        <div class="card-header border-bottom pb-0">
                            <div class="d-sm-flex align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-semibold text-lg mb-0">TAHAP 1 -- Rekonsiliasi Pekebun</h6>
                                    <p class="text-sm mb-sm-0">{{ $get_data_kelembagaan_pekebun->no_dokumen }} --
                                        {{ $get_data_kelembagaan_pekebun->koperasi }}</p>
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
                                    @if (Auth::user()->email == 'bpdpksbaru')
                                    @else
                                        <span data-bs-toggle="tooltip" data-placement="top"
                                            title="Bilamana Ditemukan Pekebun di SMART-PSR dan PSR Online">
                                            <button type="button"
                                                class="btn btn-sm btn-info btn-icon d-flex align-items-center mb-0 me-2"
                                                data-bs-target="#rekonModal"
                                                onclick="getCheckedRekon()" id="modalRekon">
                                                <span class="btn-inner--icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                                <span class="btn-inner--text">Rekon</span>
                                            </button>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="Bilamana Hanya Ditemukan Transaksi PSR Online">
                                            <button type="button"
                                                class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 me-2"
                                                data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                                <span class="btn-inner--icon me-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path
                                                            d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34">
                                                        </path>
                                                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                                    </svg>
                                                </span>
                                                <span class="btn-inner--text">Adjustment</span>
                                            </button>
                                        </span>
                                        <button type="button"
                                            class="btn btn-sm btn-success btn-icon d-flex align-items-center mb-0 me-2"
                                            data-bs-toggle="modal" data-bs-target="#tambahTransaksiModal">
                                            <span class="btn-inner--icon me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 3h18v18H3zM12 8v8m-4-4h8" />
                                                </svg>
                                            </span>
                                            <span class="btn-inner--text">Tambah Pekebun</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="table-responsive">
                                        <table class="table align-items-center justify-content-center mb-0"
                                            id="">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Nama</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        NIK</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Checklist</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data_pekebun_psr_online as $pekebun_psr_online)
                                                    <tr>
                                                        <td>{{ $pekebun_psr_online->nama_pekebun }}</td>
                                                        <td>{{ $pekebun_psr_online->no_ktp }}</td>
                                                        <td><input type="checkbox" name="checkbox_pekebun_psr_online"
                                                                value="{{ $pekebun_psr_online->id_pekebun }}" nik_pekebun_psr_online="{{ $pekebun_psr_online->no_ktp }}" nama_pekebun_psr_online="{{ $pekebun_psr_online->nama_pekebun }}"></td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="table-responsive">
                                        <table class="table align-items-center justify-content-center mb-0"
                                            id="">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Nama</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        NIK</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Checklist</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data_pekebun_smart_psr as $pekebun_smart_psr)
                                                    <tr>
                                                        <td>{{ $pekebun_smart_psr->nama_pekebun }}</td>
                                                        <td>{{ $pekebun_smart_psr->nik }}</td>
                                                        <td><input type="checkbox" name="checkbox_pekebun_smart_psr"
                                                                value="{{ $pekebun_smart_psr->id }}"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card  shadow-xs mb-4">

                        <div class="card-body border-1 mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="accordion" id="accordionGeneralLedger">
                                        <div class="accordion-item mb-3">
                                            <h5 class="accordion-header" id="headingGeneralLedger">
                                                <button
                                                    class="accordion-button border-bottom font-weight-bold collapsed"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseGeneralLedger" aria-expanded="false"
                                                    aria-controls="collapseGeneralLedger">
                                                    Data Ledger Pekebun {{ $get_data_kelembagaan_pekebun->no_dokumen }}
                                                    <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                                        aria-hidden="true"></i>
                                                    <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                                        aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                            <br>
                                            <h5>Jumlah Pekebun : {{count($data_pekebun_sudah_rekon)}} Pekebun</h5>
                                            @php
                                                $luas_lahan_rekon = $data_pekebun_sudah_rekon->sum('luas_lahan');
                                            @endphp
                                            <h5>Luas Lahan : {{number_format($luas_lahan_rekon,4,'.',',')}} Hektar</h5>
                                            <div id="collapseGeneralLedger" class="accordion-collapse"
                                                aria-labelledby="headingGeneralLedger"
                                                data-bs-parent="#accordionGeneralLedger" style="color:black;">
                                                <div class="accordion-body text-sm opacity-8 text-black">
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table align-items-center justify-content-center mb-0"
                                                            id="tableGeneralLedger" style="width: 100%">
                                                            <thead>
                                                                <tr>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        No</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Nama</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        NIK</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Luas SK Dirut</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Di Rekon Oleh</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Di Rekon Tanggal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $i = 1;
                                                                @endphp
                                                                @foreach ($data_pekebun_sudah_rekon as $data_pekebun)
                                                                <tr>
                                                                    <td>{{$i++}}</td>
                                                                    <td>{{$data_pekebun->nama_pekebun}}</td>
                                                                    <td>{{$data_pekebun->nik_pekebun}}</td>
                                                                    <td>{{number_format($data_pekebun->luas_lahan,4,'.',',')}}</td>
                                                                    <td>{{$data_pekebun->created_by}}</td>
                                                                    <td>{{$data_pekebun->created_at}}</td>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- Modal Rekon -->
    <div class="modal fade" id="rekonModal" tabindex="-1" role="dialog" aria-labelledby="rekonsiliasiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rekonsiliasiLabel">Rekon Pekebun</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('rekon_tahap_1')}}" method="post" enctype="multipart/form-data" id="postRekon"
                    onsubmit="submitModalRekon.disabled = true; return true;">
                    <div class="modal-body">
                        @csrf
                        <label for="kredit" class="col-form-label">Nama Pekebun:</label>
                        <input type="text" class="form-control" name="nama_pekebun_rekon" id="nama_pekebun_rekon" value=""
                            form="postRekon" readonly>

                        <label for="debit" class="col-form-label">NIK:</label>
                        <input type="text" class="form-control" name="nik_pekebun_rekon" id="nik_pekebun_rekon" value=""
                            form="postRekon" readonly>
                        <br>
                        <h5 style="color: red">Pastikan Data Sudah Benar!!!</h5>
                        <div id="selisih"></div>
                        <input type="hidden" class="form-control" name="id_pekebun_psr_online" id="id_pekebun_psr_online_rekon" form="postRekon" value="">
                        <input type="hidden" class="form-control" name="id_pekebun_smart_psr" id="id_pekebun_smart_psr_rekon" form="postRekon" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> <i
                                class="fas fa-ban"></i> Cancel</button>
                        <button id="btnRekon" class="btn btn-primary float-right" type="submit" form="postRekon"
                            name="submitModalRekon"> <i class="fas fa-check"></i> Rekon!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Rekon-->

    <!-- Modal Adjustment -->
    <div class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustmentLabel">New message</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" id="postAdjustment"
                    onsubmit="submitModalAdjustment.disabled = true; return true;">
                    <div class="modal-body">
                        @csrf
                        <label for="kredit" class="col-form-label">Nama Pekebun:</label>
                        <input type="text" class="form-control" name="nama_pekebun_adjustment" id="nama_pekebun_adjustment" value=""
                            oninput="ceknama_pekebun()" form="postAdjustment" readonly>

                        <label for="debit" class="col-form-label">NIK:</label>
                        <input type="text" class="form-control" name="nik_pekebun_adjustment" id="nik_pekebun_adjustment" value=""
                            oninput="ceknik_pekebun()" form="postAdjustment" readonly>
                        <br>
                        <h5 style="color: red">Pastikan Data Sudah Benar!!!</h5>
                        <input type="hidden" class="form-control" name="id_transaksiAdjustment"
                            form="postAdjustment">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i
                                class="fas fa-ban"></i> Cancel</button>
                        <button id="btnAdjustment" class="btn btn-info float-right" type="submit"
                            form="postAdjustment" name="submitModalAdjustment"> <i class="fas fa-edit"></i>
                            Adjustment!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Adjustment-->

    <!-- Modal Transaksi -->
    <div class="modal fade" id="tambahTransaksiModal" tabindex="-1" role="dialog"
        aria-labelledby="transaksiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transaksiLabel">Tambah Transaksi</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" id="postTambahTransaksi"
                    onsubmit="submitFormTambahTransaksi.disabled = true; return true;">
                    <div class="modal-body">
                        @csrf
                        <label for="kredit" class="col-form-label">Nama Pekebun:</label>
                        <input type="text" class="form-control" name="nama_pekebun_adjustment" id="nama_pekebun_adjustment"
                            oninput="ceknama_pekebun()" form="postTambahTransaksi">

                        <label for="debit" class="col-form-label">NIK:</label>
                        <input type="text" class="form-control" name="nik_pekebun_adjustment" id="nik_pekebun_adjustment"
                            oninput="ceknik_pekebun()" form="postTambahTransaksi">
                        <br>
                        <h5 style="color: red">Pastikan Data Sudah Benar!!!</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i
                                class="fas fa-ban"></i> Cancel</button>
                        <button id="btnTambahTransaksi" class="btn btn-success float-right" type="submit"
                            form="postTambahTransaksi" name="submitFormTambahTransaksi"> <i class="fas fa-plus"></i>
                            Tambah
                            Transaksi!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Transaksi-->
</x-app-layout>
<script>
    function getCheckedRekon() {

        var checkbox_psr_online = $('input[name="checkbox_pekebun_psr_online"]:checked').length;
        var checkbox_smart_psr = $('input[name="checkbox_pekebun_smart_psr"]:checked').length;

        console.log(checkbox_psr_online,checkbox_smart_psr);


        if (checkbox_psr_online === 0 && checkbox_smart_psr === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak ada Data yang Dipilih!',
                text: 'Pilihlah Satu Pekebun PSR Online dan Pekebun SMART-PSR',
            });
        } else if (checkbox_psr_online === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak ada Pekebun PSR Online yang Dipilih!',
                text: 'Pilihlah Satu Pekebun PSR Online!',
            });
        } else if (checkbox_smart_psr === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak ada Pekebun SMART-PSR yang Dipilih!',
                text: 'Pilihlah Satu Pekebun SMART-PSR!',
            });
        } else if (checkbox_smart_psr >= 2 || checkbox_psr_online >= 2) {
            Swal.fire({
                icon: 'error',
                title: 'Hanya Boleh Memilih Satu - Satu!',
                text: 'Pilihlah Satu Pekebun PSR Online dan Pekebun SMART-PSR!',
            });
        } else {
            let id_psr_online = $('input[name="checkbox_pekebun_psr_online"]:checked')[0]['value'];
            let id_smart_psr = $('input[name="checkbox_pekebun_smart_psr"]:checked')[0]['value'];
            let nama_pekebun = $('input[name="checkbox_pekebun_psr_online"]:checked')[0].getAttribute('nama_pekebun_psr_online');
            let nik_pekebun = $('input[name="checkbox_pekebun_psr_online"]:checked')[0].getAttribute('nik_pekebun_psr_online');


            // Set Nama Pekebun di Modal Berdasarkan Checklist
            $("#nama_pekebun_rekon").attr('value',nama_pekebun);
            $("#nik_pekebun_rekon").attr('value',nik_pekebun);
            // Set NIK Pekebun di Modal Berdasarkan Checklist
            $('#rekonModal').modal('show');

            $('#id_pekebun_psr_online_rekon').attr('value',id_psr_online);

            $('#id_pekebun_smart_psr_rekon').attr('value',id_smart_psr);
        }

    }
    $(document).ready(function() {


        $('#tableGeneralLedger').DataTable({});

    });
</script>
