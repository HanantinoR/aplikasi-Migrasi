<x-app-layout>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />
        <div class="container-fluid py-4 px-5">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="d-md-flex align-items-center mb-3 mx-2">
                        <div class="mb-md-0 mb-3">
                            <h3 class="font-weight-bold mb-0">Hello, Noah</h3>
                            <p class="mb-0">Apps you might like!</p>
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
                                    <h6 class="font-weight-semibold text-lg mb-0">TAHAP 1</h6>
                                    <p class="text-sm mb-sm-0">Detail -- Nama Lemabaga</p>
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
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="Bilamana Ditemukan Transaksi di SMART-PSR dan List Transaksi">
                                            <a href="" type="button" class="btn btn-sm btn-info btn-icon d-flex align-items-center mb-0 me-2" data-bs-toggle="modal" data-bs-target="#rekonModal">
                                                <span class="btn-inner--icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                                <span class="btn-inner--text">Rekon</span>
                                            </a>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="Bilamana Hanya Ditemukan Transaksi List Transaksi">
                                            <a href="" type="button" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 me-2" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                                <span class="btn-inner--icon me-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                                    </svg>
                                                </span>
                                                <span class="btn-inner--text">Adjustment</span>
                                            </a>
                                        </span>
                                        <a href="" type="button" class="btn btn-sm btn-success btn-icon d-flex align-items-center mb-0 me-2"  data-bs-toggle="modal" data-bs-target="#tambahTransaksiModal">
                                            <span class="btn-inner--icon me-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 3h18v18H3zM12 8v8m-4-4h8"/>
                                                </svg>
                                            </span>
                                            <span class="btn-inner--text">Tambah Transaksi</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="table-responsive">
                                        <table class="table align-items-center justify-content-center mb-0" id="">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Tanggal Transfer</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Kode Permohonan</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Debit</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Keterangan</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Checklist</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2024-04-19</td>
                                                    <td>PRO123131312</td>
                                                    <td>RP 100,0-</td>
                                                    <td>OKE</td>
                                                    <td><input type="checkbox" name="checkboxPermohonan" id=""></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="table-responsive">
                                        <table class="table align-items-center justify-content-center mb-0" id="">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Tanggal</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Kredit</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Debit</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        CoA</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Catatan</th>
                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                        Checklist</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2024-08-23</td>
                                                    <td>RP 300,0-</td>
                                                    <td>RP 100,0-</td>
                                                    <td>Dana PSR</td>
                                                    <td>Mantap</td>
                                                    <td><input type="checkbox" name="checkboxTransaksi" id=""></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-1 mt-4">
                            <div class="row">
                                <div class="col">
                                    <div class="accordion" id="accordionGeneralLedger">
                                        <div class="accordion-item mb-3">
                                            <h5 class="accordion-header" id="headingGeneralLedger">
                                                <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneralLedger" aria-expanded="false" aria-controls="collapseGeneralLedger">
                                                    Data General Ledger
                                                    <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                    <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                            <div id="collapseGeneralLedger" class="accordion-collapse collapse" aria-labelledby="headingGeneralLedger" data-bs-parent="#accordionGeneralLedger" style="color:black;">
                                                <div class="accordion-body text-sm opacity-8 text-black" >
                                                    <div class="table-responsive">
                                                        <table class="table align-items-center justify-content-center mb-0" id="tableGeneralLedger" style="width: 100%">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        No</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Tanggal Transaksi</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Kredit</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Debit</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Saldo Akhir</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Keterangan Tansaksi</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Catatan Transaksi</th>
                                                                    <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Edit</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="accordion" id="accordionCekSaldo">
                                        <h5 class="accordion-header" id="headingSaldo">
                                            <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSaldo" aria-expanded="false" aria-controls="collapseSaldo">
                                                Cek Saldo
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                            </button>
                                        </h5>
                                        <div id="collapseSaldo" class="accordion-collapse collapse" aria-labelledby="headingSaldo" data-bs-parent="#accordionCekSaldo" style="color:black;">
                                            <div class="accordion-body text-sm opacity-8 text-black">
                                                <div class="table-responsive">
                                                    <table class="table align-items-center justify-content-center mb-0" id="tableCekSaldo" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                    No</th>
                                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                    Tanggal Cek Saldo</th>
                                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                    Saldo</th>
                                                                <th class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                    Detail</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
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
    </main>

    <!-- Modal Rekon -->
    <div class="modal fade" id="rekonModal" tabindex="-1" role="dialog" aria-labelledby="rekonsiliasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="rekonsiliasiLabel">Rekon</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" id="postRekon"
                    onsubmit="submitModalRekon.disabled = true; return true;">
                    <div class="modal-body">
                        @csrf
                        <label for="tanggal" class="col-form-label">Tanggal: </label>
                        <input type="date" min="2015-01-01" max="{{ date('Y-m-d') }}"
                            class="form-control" name="tanggalRekon" id="tanggal"
                            oninput="cekTanggal()" form="postRekon">

                        <label for="kredit" class="col-form-label">Kredit:</label>
                        <input type="text" class="form-control" name="kreditRekon"
                            id="kreditRekon" oninput="cekKreditRekon()" form="postRekon"
                            readonly>

                        <label for="debit" class="col-form-label">Debit:</label>
                        <input type="text" class="form-control" name="debitRekon"
                            id="debitRekon" oninput="cekDebitRekon()" form="postRekon"
                            readonly>

                        <label for="keterangan" class="col-form-label">Chart of
                            Accounting:</label>
                        <br>
                        <select class="form-control col-12" name="coaRekon" id="coa"
                            form="postRekon">
                            <option value="">--- PILIH SALAH SATU ---</option>
                            <option value="BELANJA PSR">BELANJA PSR</option>
                            <option value="BELANJA LAINNYA">BELANJA LAINNYA</option>
                            <option value="BIAYA ADMINISTRASI">BIAYA ADMINISTRASI</option>
                            <option value="BIAYA TRANSFER">BIAYA TRANSFER</option>
                            <option value="BLOKIR SALDO">BLOKIR SALDO</option>
                            <option value="BUKA BLOKIR">BUKA BLOKIR</option>
                            <option value="DANA PSR">DANA PSR</option>
                            <option value="DANA LAINNYA">DANA LAINNYA</option>
                            <option value="BUNGA">BUNGA</option>
                            <option value="PAJAK">PAJAK</option>
                            <option value="HAPUS BLOKIR">HAPUS BLOKIR</option>
                            <option value="HAPUS SALDO">HAPUS SALDO</option>
                            <option value="PENGEMBALIAN">PENGEMBALIAN</option>
                            <option value="RETUR BELANJA PSR">RETUR BELANJA PSR</option>
                            <option value="RETUR BELANJA LAINNYA">RETUR BELANJA LAINNYA
                            </option>
                        </select>
                        <label for="catatan" class="col-form-label">Catatan:</label>
                        <input type="text" class="form-control" name="catatanRekon"
                            id="catatan" oninput="cekCatatan()" form="postRekon">
                        <br>
                        <div id="selisih"></div>
                        <input type="hidden" class="form-control" name="id_permohonanRekon"
                            form="postRekon">
                        <input type="hidden" class="form-control" name="id_transaksiRekon"
                            form="postRekon">
                        <input type="hidden" class="form-control" name="id_transfer_manual"
                            form="postRekon">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i
                            class="fas fa-ban"></i> Cancel</button>
                        <button id="btnRekon" class="btn btn-primary float-right" type="submit"
                            form="postRekon" name="submitModalRekon"> <i
                                class="fas fa-check"></i> Rekon!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Rekon-->

    <!-- Modal Adjustment -->
    <div class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentLabel" aria-hidden="true">
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
                        <label for="tanggal" class="col-form-label">Tanggal: </label>
                        <input type="date" min="2015-01-01" max="{{ date('Y-m-d') }}"
                            class="form-control" name="tanggalAdjustment" id="tanggal"
                            oninput="cekTanggal()" form="postAdjustment">
                        <label for="kredit" class="col-form-label">Kredit:</label>
                        <input type="text" class="form-control" name="kreditAdjustment"
                            id="kreditAdjustment" oninput="cekKreditAdjustment()"
                            form="postAdjustment" readonly>
                        <label for="debit" class="col-form-label">Debit:</label>
                        <input type="text" class="form-control" name="debitAdjustment"
                            id="debitAdjustment" oninput="cekDebitAdjustment()"
                            form="postAdjustment" readonly>
                        <label for="keterangan" class="col-form-label">Chart of
                            Accounting:</label>
                        <br>
                        <select class="form-control col-12" name="coaAdjustment"
                            id="coa" onchange="cekCoA()" form="postAdjustment"
                            required>
                            <option value="" disabled>--- PILIH SALAH SATU ---</option>
                            <option value="BELANJA PSR">BELANJA PSR</option>
                            <option value="BELANJA LAINNYA">BELANJA LAINNYA</option>
                            <option value="BIAYA ADMINISTRASI">BIAYA ADMINISTRASI</option>
                            <option value="BIAYA TRANSFER">BIAYA TRANSFER</option>
                            <option value="BLOKIR SALDO">BLOKIR SALDO</option>
                            <option value="BUKA BLOKIR">BUKA BLOKIR</option>
                            <option value="DANA PSR">DANA PSR</option>
                            <option value="DANA LAINNYA">DANA LAINNYA</option>
                            <option value="BUNGA">BUNGA</option>
                            <option value="PAJAK">PAJAK</option>
                            <option value="HAPUS BLOKIR">HAPUS BLOKIR</option>
                            <option value="HAPUS SALDO">HAPUS SALDO</option>
                            <option value="PENGEMBALIAN">PENGEMBALIAN</option>
                            <option value="RETUR BELANJA PSR">RETUR BELANJA PSR</option>
                            <option value="RETUR BELANJA LAINNYA">RETUR BELANJA LAINNYA
                            </option>
                        </select>
                        <div id="asd">
                        </div>
                        <input type="hidden" class="form-control"
                            name="id_transaksiAdjustment" form="postAdjustment">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i
                            class="fas fa-ban"></i> Cancel</button>
                        <button id="btnAdjustment" class="btn btn-info float-right"
                            type="submit" form="postAdjustment" name="submitModalAdjustment"> <i
                                class="fas fa-edit"></i> Adjustment!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Adjustment-->

    <!-- Modal Transaksi -->
    <div class="modal fade" id="tambahTransaksiModal" tabindex="-1" role="dialog" aria-labelledby="transaksiLabel" aria-hidden="true">
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
                        <label for="tanggal" class="col-form-label">Tanggal: </label>
                        <input type="date" min="2015-01-01" max="{{ date('Y-m-d') }}"
                            class="form-control" name="tanggalTambahTransaksi" id="tanggal"
                            oninput="cekTanggal()" form="postTambahTransaksi" required>
                        <label for="kredit" class="col-form-label">Kredit:</label>
                        <input type="text" class="form-control"
                            name="kreditTambahTransaksi" id="kreditTambahTransaksi"
                            oninput="cekKreditTambahTransaksi()" form="postTambahTransaksi">
                        <label for="debit" class="col-form-label">Debit:</label>
                        <input type="text" class="form-control"
                            name="debitTambahTransaksi" id="debitTambahTransaksi"
                            oninput="cekDebitTambahTransaksi()" form="postTambahTransaksi">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i
                            class="fas fa-ban"></i> Cancel</button>
                        <button id="btnTambahTransaksi" class="btn btn-success float-right"
                            type="submit" form="postTambahTransaksi"
                            name="submitFormTambahTransaksi"> <i class="fas fa-plus"></i> Tambah
                            Transaksi!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Modal Transaksi-->
</x-app-layout>
<script>
    $(document).ready(function(){
        let dataGeneralLedger = [
            [
                "1",
                "2024-12-31",
                "Rp 957.450.000,00",
                "Rp 100.450.000,00",
                "Rp 957.450.000,00",
                "Dana PSR",
                "",
                `<button class="btn btn-success btn-sm">
                    <span class="btn-inner--icon me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                            <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                        </svg>
                    </span>
                    <span class="btn-inner--text">Edit</span>
                </button>`,
            ],
        ];

        let dataSaldo = [
            [
                "1",
                "2024-12-31",
                "RP. -0,",
                "LAGI CARI DUIT BENTAR",
            ],
        ]

        $('#tableGeneralLedger').DataTable({
            data:dataGeneralLedger,
        });

        $('#tableCekSaldo').DataTable({
            data:dataSaldo,
        });
    });
</script>
