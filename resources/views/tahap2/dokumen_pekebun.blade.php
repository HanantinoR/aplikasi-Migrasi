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
                                    <h6 class="font-weight-semibold text-lg mb-0">TAHAP 2 -- <strong>Rekonsiliasi
                                            DOKUMEN Legalitas dan Kelengkapan Administrasi Pekebun</strong></h6>
                                    <p class="text-sm mb-sm-0"><strong>{{ @$get_data_pekebun->nama_pekebun }}</strong>
                                    </p>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card  shadow-xs mb-4">
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-10 text-start mt-4">
                                    <h5>
                                        Dokumen Pekebun
                                    </h5>
                                </div>
                            </div>
                            <hr class="horizontal dark mt-2">
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        @php
                                            // dd($get_dokumen_psr_online);
                                        @endphp
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Scan KTP
                                        </p>
                                        @if ($get_dokumen_psr_online->fc_ktp === null || $get_dokumen_psr_online->fc_ktp === "null" || $get_dokumen_psr_online->fc_ktp === "")
                                        <a href="#"
                                            class="btn btn-sm btn-danger btn-icon align-items-center mb-0 me-2">
                                            <span class="btn-inner--text"> !!! Scan KK Tidak Tersedia di PSR Online !!!</span>
                                        </a>
                                        @else
                                        <a href="https://program-psr.bpdp.or.id/assets/upload_file/pekebun/{{$get_dokumen_psr_online->fc_ktp}}"
                                            target="blank"
                                            class="btn btn-sm btn-primary btn-icon align-items-center mb-0 me-2">
                                            <i class="fa-solid fa-link fa-2xl"></i>
                                            <span class="btn-inner--text"> Klik Disini Untuk Melihat Scan KTP</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Scan KK
                                        </p>
                                        @if ($get_dokumen_psr_online->fc_kk === null || $get_dokumen_psr_online->fc_kk === "null" || $get_dokumen_psr_online->fc_kk === "")
                                        <a href="#"
                                            class="btn btn-sm btn-danger btn-icon align-items-center mb-0 me-2">
                                            <span class="btn-inner--text"> !!! Scan KK Tidak Tersedia di PSR Online !!!</span>
                                        </a>
                                        @else
                                        <a href="https://program-psr.bpdp.or.id/assets/upload_file/pekebun/{{$get_dokumen_psr_online->fc_kk}}"
                                            target="blank"
                                            class="btn btn-sm btn-primary btn-icon align-items-center mb-0 me-2">
                                            <i class="fa-solid fa-link fa-2xl"></i>
                                            <span class="btn-inner--text"> Klik Disini Untuk Melihat Scan KK</span>
                                        </a>
                                        @endif
                                        <p id="error_nama_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_nama_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Nama Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->nama_pekebun }}">
                                        </h6>
                                        <p id="error_nama_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_nama_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            NIK Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->nik_pekebun }}">
                                        </h6>
                                        <p id="error_nik_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_nik_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            KK Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->kk_pekebun }}">
                                        </h6>
                                        <p id="error_kk_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_kk_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Tempat Lahir Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->tempat_lahir_pekebun }}">
                                        </h6>
                                        <p id="error_tempat_lahir_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_tempat_lahir_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Tanggal Lahir Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->tanggal_lahir_pekebun }}">
                                        </h6>
                                        <p id="error_tanggal_lahir_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_tanggal_lahir_pekebun_keterangan"
                                            class="text-danger text-xs pt-1" style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Jenis Kelamin Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->jenis_kelamin_pekebun }}">
                                        </h6>
                                        <p id="error_jenis_kelamin_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_jenis_kelamin_pekebun_keterangan"
                                            class="text-danger text-xs pt-1" style="display:none;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Status Pernikahan Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->status_pernikahan_pekebun }}">
                                        </h6>
                                        <p id="error_status_pernikahan_pekebun_sesuai"
                                            class="text-danger text-xs pt-1" style="display:none;"></p>
                                        <p id="error_status_pernikahan_pekebun_keterangan"
                                            class="text-danger text-xs pt-1" style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Handphone Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->nomor_hp_pekebun }}">
                                        </h6>
                                        <p id="error_nomor_hp_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_nomor_hp_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Alamat Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <textarea class="form form-control">{{ @$get_data_pekebun->alamat_pekebun }}</textarea>
                                        </h6>
                                        <p id="error_alamat_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_alamat_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Provinsi Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->provinsi_pekebun }}">
                                        </h6>
                                        <p id="error_provinsi_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_provinsi_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Kota / Kabupaten Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->kota_kabupaten_pekebun }}">
                                        </h6>
                                        <p id="error_kota_kabupaten_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_kota_kabupaten_pekebun_keterangan"
                                            class="text-danger text-xs pt-1" style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Kecamatan Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->kecamatan_pekebun }}">
                                        </h6>
                                        <p id="error_kecamatan_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_kecamatan_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Kelurahan / Desa Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->kelurahan_pekebun }}">
                                        </h6>
                                        <p id="error_kelurahan_pekebun_sesuai" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                        <p id="error_kelurahan_pekebun_keterangan" class="text-danger text-xs pt-1"
                                            style="display:none;"></p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100">
                                        <p class="mb-0 font-weight-bold text-sm">
                                            Kode Pos Pekebun
                                        </p>
                                        <h6 class="mb-1">
                                            <input type="text" class="form form-control"
                                                value="{{ @$get_data_pekebun->kodepos_pekebun }}">
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-check form-check-info text-start mb-4">
                                        <div class="col-lg-2 pt-2">
                                            <div class="h-100">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio"
                                                            name="radio_nomor_hp_pekebun" id="radio_nomor_hp_pekebun"
                                                            value="true">
                                                        Sesuai
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio"
                                                            name="radio_nomor_hp_pekebun" id="radio_nomor_hp_pekebun"
                                                            value="false">
                                                        Tidak Sesuai
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="h-100">
                                                <h6 class="mb-1">
                                                    <textarea class="form form-control" id="text_ket_nomor_hp_pekebun" placeholder="Keterangan..."></textarea>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="form-check form-check-info text-start mb-4">
                                            <label class="form-check-label text-lg" for="pernyataan_data_sesuai">
                                                <input class="form-check-input mt-2" type="checkbox" name="pernyataan_data_sesuai" id="pernyataan_data_sesuai" style="height:40px;width:40px;" required >
                                                <div class="d-flex align-items-center ps-3 mt-2" style="height:40px;width:auto;">
                                                    <span>
                                                        Dengan ini, Saya
                                                        <span class="text-bold text-info">bertanggung jawab</span>
                                                        atas kesalahan atau kelalaian apapun dalam proses penginputan dan data yang saya masukkan adalah
                                                        <span class="text-bold text-info">benar dan sesuai</span>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="h-100">
                                        <button id="simpan_verifikasi" class="btn btn-primary float-end">
                                            Simpan &nbsp;&nbsp;<i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-app-layout>
<script>
    $(document).ready(function() {
        const id_pekebunnya_proposal = {{ $get_data_pekebun->id }};
        $('#simpan_verifikasi').on('click',function(){
            Swal.fire({
                title: 'Simpan',
                html: 'Apakah Anda yakin ingin verifikasi informasi ini sudah benar?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed){
                    $('#simpan_verifikasi').prop('disabled',true);

                    var form_data = new FormData();
                    form_data.append('id_pekebunnya_proposal',id_pekebunnya_proposal);
                    $.each($('input[type="radio"]:checked'),function(index,input){
                        let name = $(input).attr('name');
                        if(name.indexOf('modal_') == -1){
                            let key = $(input).attr('name').replace('radio_','');
                            let value = $(input).val();
                            let keterangan_verifikasi = $(`#text_ket_${key.replace(/[<>:"\/\\|()?*]/g,'\\$&')}`).val();

                            form_data.append(`${key}[sesuai]`,value);
                            form_data.append(`${key}[keterangan]`,keterangan_verifikasi);
                        }
                    });

                    $('p.text-danger').html('').hide();
                    $.ajax({
                        url: "{{ route('tahap2.post_dokumen_pekebun') }}",
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                        },
                        data: form_data,
                        id_pekebun:id_pekebunnya_proposal,
                        success: function(data){
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: data.message
                            }).then(function(){
                                window.location = "{{ url()->previous() }}";
                            });
                        },error: function(xhr, status, error) {
                            $('#simpan_verifikasi').prop('disabled',false);
                            var response = JSON.parse(xhr.responseText);
                            if(response.errors){
                                // Show error messages if any
                                $.each(response.errors,function(key,value){
                                    key = key.split('.').join('_').replace(/[<>:"\/\\|()?*]/g,'\\$&');
                                    $('#error_'+key).show().html(value[0]);
                                });
                            }
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message,
                            });
                        }
                    });
                }
            });
        });

        $('#simpan_verifikasi').prop('disabled',true);
        $('#pernyataan_data_sesuai').on('change', function () {
            if($('#pernyataan_data_sesuai').is(':checked')){
                $('#simpan_verifikasi').prop('disabled',false);
            } else {
                $('#simpan_verifikasi').prop('disabled',true);
            }
        });
    });
</script>
