<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />
        <div class="container-fluid py-4">
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
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card card-profile">
                        <div class="card-body pt-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10 text-start mt-4">
                                        <h4>
                                            Edit Password Akun
                                        </h4>
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-4">
                                <div class="row">
                                    <div class="col-lg-12 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Nama Pengguna
                                            </p>
                                            <input type="text" readonly id="nama_pengguna" name="nama_pengguna" class="form form-control" placeholder="Masukkan Nama Pengguna Baru" required value="{{@$users->name}}">
                                            <p id="error_nama_pengguna" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Jabatan
                                            </p>
                                            <select id="jabatan" name="jabatan"
                                                class="select-select2 form-select" style="width:100%"
                                                data-placeholder="-- Pilih Jabatan Pengguna Baru --"
                                                aria-label="jabatan" disabled required>
                                                <option value=""></option>
                                                <option value="Direktorat Jenderal Perkebunan" @if(str_contains(@$users->roles,"Direktorat Jenderal Perkebunan")) selected @endif>Direktorat Jenderal Perkebunan</option>
                                                <option value="Dinas Kabupaten" @if(@$users->roles == "Dinas Kabupaten") selected @endif>Dinas Kabupaten</option>
                                                <option value="Dinas Provinsi" @if(@$users->roles == "Dinas Provinsi") selected @endif>Dinas Provinsi</option>
                                            </select>
                                            <p id="error_jabatan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-4" id="select_unit_kerja_dinas">
                                        <div class="h-100" >
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Unit Kerja
                                            </p>
                                            <select id="unit_kerja" name="unit_kerja"
                                                class="select-select2 form-select" style="width:100%"
                                                data-placeholder="-- Pilih Jabatan Terlebih Dahulu --"
                                                aria-label="unit_kerja" disabled required>
                                            </select>
                                            <option value=""></option>
                                            <p id="error_unit_kerja" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Password Baru Pengguna
                                            </p>
                                            <input type="text" id="password_pengguna" name="password_pengguna" class="form form-control" placeholder="Masukkan Password Baru Pengguna" required>
                                            <p id="error_password_pengguna" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-4">
                                        <div class="h-100">
                                            <button type="button" class="btn btn-primary w-100" id="btn_simpan">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @csrf
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
        $('.select-select2').select2();

        $('#jabatan').ready(function() {
            var jabatan = $('#jabatan').val();
            $('#unit_kerja').html('<option value="" selected></option>');

            $.ajax({
                url: "{{ route('post_daftar_unit_kerja_dinas') }}",
                method: "POST",
                data: {
                    jabatan: jabatan,
                },
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function(data) {
                    $.each(data.unit_kerja_dinas, function(i, item) {
                        if (item.id == '{{$users->location}}') {
                            $('#unit_kerja').append('<option value="' + item.id + '" selected>' +
                            item.nama_lengkap_dinas + '</option>');
                        }else{
                            $('#unit_kerja').append('<option value="' + item.id + '">' +
                            item.nama_lengkap_dinas + '</option>');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error
                    });
                }
            });
        });


        $('#jabatan').change(function() {
            var jabatan = $('#jabatan').val();
            $('#unit_kerja').html('<option value="" selected></option>');

            $.ajax({
                url: "{{ route('post_daftar_unit_kerja_dinas') }}",
                method: "POST",
                data: {
                    jabatan: jabatan,
                },
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function(data) {
                    $.each(data.unit_kerja_dinas, function(i, item) {
                        $('#unit_kerja').append('<option value="' + item.id + '">' +
                            item.nama_lengkap_dinas + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error
                    });
                }
            });
        });

    });

    $('#btn_simpan').on('click', function () {
            Swal.fire({
                icon: 'info',
                title: 'Simpan Data',
                text: 'Apakah Anda ingin mendaftarkan akun ini?',
                showCancelButton: true,
                confirmButtonText: 'Daftarkan',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                            let password_pengguna = $('#password_pengguna').val();

                            var form_data = new FormData();
                            form_data.append('password_pengguna',password_pengguna);
                            $('p.text-danger').hide().html('');

                            $.ajax({
                                url: "{{ route('manajemen_akun.post_edit_password',['id_pengguna'=>$users->id]) }}",
                                processData: false,
                                contentType: false,
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                                },
                                data: form_data,
                                success: function(data){
                                    resolve();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Akun Berhasil Di Edit.',
                                        confirmButtonText: 'Ok',
                                    }).then((result) => {
                                        if(result.isConfirmed){
                                            window.location.href = "{{ route('manajemen_akun.index') }}";
                                        }else{
                                            window.close();
                                        }
                                    });
                                },error: function(xhr, status, error) {
                                    var response = JSON.parse(xhr.responseText);
                                    if(response.errors){
                                        // Show error messages if any
                                        $.each(response.errors,function(key,value){
                                            $('#error_'+key).show().html(value);
                                        });
                                    }
                                    reject(response.message);
                                }
                            });
                    }).catch((error) => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            html: error,
                        });
                    });
                }
            });
        });

</script>
