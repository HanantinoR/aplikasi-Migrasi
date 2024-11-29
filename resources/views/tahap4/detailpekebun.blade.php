<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />
        <div class="container-fluid py-4">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card card-profile">
                        <div class="card-body pt-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10 text-start mt-4">
                                        <h4>
                                            Rekonsiliasi Titik Koordinat Legalitas - ({{ $get_data_kelembagaan_pekebun->nomor_proposal }}) / {{ $get_data_kelembagaan_pekebun->nama_kelembagaan_pekebun }}
                                        </h4>
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-4">
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Nama Pekebun
                                            </p>
                                            <h6>
                                                {{ $data_pekebun_selected->nama_pekebun }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                NIK Pekebun
                                            </p>
                                            <h6>
                                                {{ $data_pekebun_selected->nik_pekebun }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                @php
                                // dd($get_data_kelembagaan_pekebun,$get_data_legalitas_pekebun_selected,$data_pekebun_selected);
                            @endphp

                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Nomor Dokumen Legalitas Lahan
                                            </p>
                                            <input type="text" id="nomor_dokumen_legalitas_lahan" name="nomor_dokumen_legalitas_lahan" class="form form-control" placeholder="Masukkan Nomor Dokumen Legalitas Lahan" required value="{{@$get_data_legalitas_pekebun_selected->nomor_dokumen_legalitas_lahan}}">
                                            <p id="error_nomor_dokumen_legalitas_lahan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Nama Tertera di Legalitas Lahan
                                            </p>
                                            <input type="text" id="nama_tertera_dokumen_legalitas_lahan" name="nama_tertera_dokumen_legalitas_lahan" class="form form-control" placeholder="Masukkan Nama Tertera di Dokumen Legalitas Lahan" required value="{{@$get_data_legalitas_pekebun_selected->nama_tertera_dokumen_legalitas_lahan}}">
                                            <p id="error_nama_tertera_dokumen_legalitas_lahan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Jenis Dokumen Legalitas Lahan
                                            </p>
                                            <select id="jenis_dokumen_legalitas_lahan" name="jenis_dokumen_legalitas_lahan"
                                                class="select-select2 form-select" style="width:100%"
                                                data-placeholder="-- Pilih Jenis Dokumen Legalitas Lahan --"
                                                aria-label="jenis_dokumen_legalitas_lahan" required>
                                                <option value=""></option>
                                                <option value="SKT" @if(str_contains(@$get_data_legalitas_pekebun_selected->jenis_dokumen_legalitas_lahan,"SKT")) selected @endif>Surat Kepemilikan Tanah (SKT)</option>
                                                <option value="SHM" @if(@$get_data_legalitas_pekebun_selected->jenis_dokumen_legalitas_lahan == "SHM") selected @endif>Sertifikat Hak Milik (SHM)</option>
                                            </select>
                                            <p id="error_jenis_dokumen_legalitas_lahan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                Tanggal Terbit Dokumen Legalitas Lahan
                                            </p>
                                            <input id="tanggal_terbit_dokumen_legalitas_lahan" name="tanggal_terbit_dokumen_legalitas_lahan" type="date" max="{{ date('Y-m-d') }}" class="form-control" required value="{{@$get_data_legalitas_pekebun_selected->tanggal_terbit_dokumen_legalitas_lahan}}">
                                            <p id="error_tanggal_terbit_dokumen_legalitas_lahan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                File Dokumen Legalitas Lahan (PDF)
                                            </p>
                                            <input id="file_dokumen_legalitas_lahan" name="file_dokumen_legalitas_lahan" type="file" class="form-control" required>
                                            <p id="error_file_dokumen_legalitas_lahan" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4" id="toggle_file_shm_beda_nama" style="display: none;">
                                        <div class="h-100">
                                            <p class="mb-0 font-weight-bold text-sm">
                                                SHM Beda Nama (PDF) (Opsional)
                                            </p>
                                            <input id="file_shm_beda_nama" name="file_shm_beda_nama" type="file" class="form-control" required>
                                            <p id="error_file_shm_beda_nama" class='text-danger text-xs pt-1' style="display:none;"></p>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <div id="div_input_tikor" class="row">
                                    <div class="col-lg-6 mt-4 mt-md-0">
                                        <p class="mb-2 font-weight-bold text-sm">
                                            Longitude
                                        </p>
                                        @php
                                            $polygon = [];
                                            $polygon_legalitas_lahan = json_decode($get_data_legalitas_pekebun_selected->polygon_legalitas_lahan);
                                            foreach($polygon_legalitas_lahan as $index_polygon=>$item_polygon){
                                                $polygon[$index_polygon] = array_map('floatval', $item_polygon);
                                            }
                                        @endphp
                                        @foreach ($polygon as $tikor)
                                            <input type="text" name="long[]" class="form form-control" placeholder="Masukkan Longitude" value="{{ $tikor[0] }}" required>
                                        @endforeach
                                    </div>
                                    <div class="col-lg-6 mt-4 mt-lg-0">
                                        <p class="mb-2 font-weight-bold text-sm">
                                            Latitude
                                        </p>
                                        @foreach ($polygon as $tikor)
                                            <input type="text" name="lat[]" class="form form-control" placeholder="Masukkan Latitude" value="{{ $tikor[1] }}" required>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mt-4 col-12 text-center text-md-end">
                                        <button id="tambah_input_tikor" type="button" class="btn btn-secondary">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button id="hapus_input_tikor" type="button" class="btn btn-secondary">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button id="clear_preview_tikor" type="button" class="btn btn-danger" onclick="clear_preview_tikor()">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3>Pratinjau Peta Lahan &nbsp;
                                    <button type="button" class="btn btn-info mb-0" onclick="center_preview_tikor()">
                                        <i class="fas fa-search"></i>
                                    </button></h3>
                                </div>
                            </div>
                            <div class="row mt-4" style="height:70vh">
                                <div class="col-sm-12">
                                    <div id="map" style="height: 100%; width:auto;">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-check form-check-info text-start mb-4">
                                        <label class="form-check-label text-lg" for="pernyataan_luas_lahan_sesuai">
                                            <input class="form-check-input mt-2" type="checkbox" name="pernyataan_luas_lahan_sesuai" id="pernyataan_luas_lahan_sesuai" style="height:40px;width:40px;" required >
                                            <div class="d-flex align-items-center ps-3 mt-2" style="height:40px;width:auto;">
                                                <span>
                                                    Dengan ini, saya menyatakan bahwa
                                                    <span class="text-bold text-info">titik koordinat</span>
                                                    yang saya cantumkan sesuai dengan dokumen
                                                    <span class="text-bold text-info">Surat Kepemilikan Tanah</span>.
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary w-100" id="btn_simpan">
                                        Simpan
                                    </button>
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

    });

    var map = L.map('map',{
        zoomControl: false,
    }).fitWorld().setView([-2.6000285, 118.015776], 5);

    var mapLink = '<a href="https://www.esri.com/">Esri</a>';
    var wholink =
        'i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';

    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; ' + mapLink + ', ' + wholink,
            maxZoom: 18,
        }).addTo(map);

    //Append new input rows to #div_input_tikor
    function tambah_input_tikor(row_amount) {
        const long_element_string = '<input type="text" name="long[]" class="form form-control" placeholder="Masukkan Longitude" required>';
        const lat_element_string = '<input type="text" name="lat[]" class="form form-control" placeholder="Masukkan Latitude" required>';
        var long_element = long_element_string;
        var lat_element = lat_element_string;

        if (row_amount) {
            for (let i = 1; i < row_amount;i++) {
                long_element += long_element_string;
                lat_element += lat_element_string;
            }
        }

        // Long form
        $('#div_input_tikor div:first-child').append(long_element);
        // Lat form
        $('#div_input_tikor div:last-child').append(lat_element);
    }

    //Remove row from #div_input_tikor
    function hapus_input_tikor(row_amount) {
        const minimum_rows = 4;
        const existing_rows = $('#div_input_tikor input[name="long[]"]').length;
        var rows_to_remove = 1;

        // Set multiple removes if any
        if (row_amount) {
            rows_to_remove = row_amount;
            // Stop remove amount to preserve 4 existing rows
            if (existing_rows - row_amount < minimum_rows) rows_to_remove = existing_rows - minimum_rows;
        }

        // Remove rows
        if (existing_rows > 4) {
            $('#div_input_tikor input[name="long[]"]').slice(rows_to_remove*-1).remove();
            $('#div_input_tikor input[name="lat[]"]').slice(rows_to_remove*-1).remove();
        }
    }

    //Preview in map
    let preview_tikor;

    function draw_preview_tikor() {
        var arr_tikor = [];
        //Clear old preview
        if (preview_tikor && preview_tikor.getLatLngs()[0].length) {
            preview_tikor.remove();
        }

        var lat = $('input[name="lat[]"]');
        var long = $('input[name="long[]"]');

        if(lat && long){
            $.each(lat, function(index, item) {
                arr_tikor.push([item.value, long[index].value]);
            });

            //Filter empty coordinate pairs
            arr_tikor = arr_tikor.filter(subarray => !subarray.every(element => element === ''));

            //Draw location on map
            preview_tikor = L.polygon(arr_tikor).addTo(map);
        }
    }

    //Re-center preview
    function center_preview_tikor(){
        if(preview_tikor && preview_tikor.getLatLngs()[0].length){
            //To center of polygon
            // map.flyTo(preview_tikor.getBounds().getCenter(), 18, {
            //     duration: 3
            // });

            //To first coordinate of polygon
            map.flyTo(preview_tikor.getLatLngs()[0][0], 18, {
                duration: 3
            });
        }
    }

    function clear_preview_tikor(){
        Swal.fire({
            title: 'Kosongkan Input Titik Koordinat',
            text: 'Apakah Anda ingin menghapus seluruh input titik koordinat?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                $('input[name="lat[]"]').val('');
                $('input[name="long[]"]').val('');
                $('.text_luas_lahan_hitung').html('0');

                if(preview_tikor && preview_tikor.getLatLngs()[0].length){
                    preview_tikor.remove();
                }
                draw_preview_tikor();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Input titik koordinat berhasil dihapus.',
                });
            }
        });
    }

    L.control.zoom({
        position: 'topright'
    }).addTo(map);

    const id_proposal = {{$id_proposal}};
    const id_pekebun = {{$data_pekebun_selected->id}};
    const id_legalitas = {{$id_legalitas}};
    $(document).ready(function () {
        $('#tambah_input_tikor').on('click', function (e) {
            tambah_input_tikor();
            draw_preview_tikor();
        });

        $('#hapus_input_tikor').on('click', function (e) {
            hapus_input_tikor();
            draw_preview_tikor();
        });

        $('#clear_preview_tikor').on('click', function (e) {
            clear_preview_tikor();
        });

        $('#div_input_tikor').on('input', 'input', function (e) {
            var target = e.target;
            var angka = target.value;
            angka = angka.replace(/[^0123456789\-\+\.]/g, '');
            target.value = angka;

            draw_preview_tikor();
        });

        // Support pasting coordinates from Excel
        $('#div_input_tikor').on('paste', 'input', function (e) {
            // Get incoming pasted text from clipboard
            const clipboard_data = e.originalEvent.clipboardData || window.clipboardData;
            var pasted_text = clipboard_data.getData('text');

            // Data pre-processing
            pasted_text = pasted_text.replace(/\,/g,'.');
            pasted_text = pasted_text.replace(/[^0123456789\-\+\.\t\n]/g, '');
            pasted_text = pasted_text.replace(/[\n]$/g, '');
            var tikor_array = pasted_text.split('\n').map((line) => line.split('\t'));

            if(tikor_array.length < 2){
                if (tikor_array[0][0] < 80) {
                    $(this).val();
                }
            }else{
                e.preventDefault();
                var lat_field = $('input[name="lat[]"]');
                var long_field = $('input[name="long[]"]');

                $('input[name="lat[]"]').val('');
                $('input[name="long[]"]').val('');
                $('.text_luas_lahan_hitung').html('0');

                for (let i = 0; i < tikor_array.length; i++){
                    if (tikor_array[i][0] < 80) {
                        var temp_lat = tikor_array[i][0];
                        var temp_long = tikor_array[i][1];
                        tikor_array[i] = [temp_long,temp_lat];
                    }
                }

                // Prepare field to take more/fewer coordinates than fields
                // Then update the scope
                if (tikor_array.length > long_field.length) {
                    var missing_row_amount = tikor_array.length - long_field.length;
                    tambah_input_tikor(missing_row_amount);
                } else if (tikor_array.length < long_field.length) {
                    var excess_row_amount = long_field.length - tikor_array.length;
                    hapus_input_tikor(excess_row_amount);
                }
                lat_field = $('input[name="lat[]"]');
                long_field = $('input[name="long[]"]');

                // Set longitudes
                for (let i = 0; i < long_field.length; i++) {
                    if (tikor_array[i]) {
                        $(long_field[i]).val(tikor_array[i][0]);
                    } else {
                        $(long_field[i]).val('');
                    }
                }

                // Set latitudes
                for (let i = 0; i < lat_field.length; i++) {
                    if (tikor_array[i]) {
                        $(lat_field[i]).val(tikor_array[i][1]);
                    } else {
                        $(lat_field[i]).val('');
                    }
                }
            }
            draw_preview_tikor();
        });

        $('#btn_simpan').prop('disabled',true);
        $('#pernyataan_luas_lahan_sesuai').on('change', function () {
            if($('#pernyataan_luas_lahan_sesuai').is(':checked')){
                $('#btn_simpan').prop('disabled',false);
            } else {
                $('#btn_simpan').prop('disabled',true);
            }
        });

        $('#btn_simpan').on('click', function () {
            Swal.fire({
                icon: 'info',
                title: 'Simpan Input Titik Koordinat',
                text: 'Apakah Anda ingin menyimpan input titik koordinat Anda?',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        if(preview_tikor && preview_tikor.getLatLngs()[0].length){
                            let nama_tertera_dokumen_legalitas_lahan = $('#nama_tertera_dokumen_legalitas_lahan').val();
                            let nomor_dokumen_legalitas_lahan = $('#nomor_dokumen_legalitas_lahan').val();
                            let jenis_dokumen_legalitas_lahan = $('#jenis_dokumen_legalitas_lahan').val();
                            let tanggal_terbit_dokumen_legalitas_lahan = $('#tanggal_terbit_dokumen_legalitas_lahan').val();
                            let file_dokumen_legalitas_lahan = $('#file_dokumen_legalitas_lahan')[0].files[0] ?? '';
                            let file_shm_beda_nama = $('#file_shm_beda_nama')[0].files[0] ?? '';
                            let longitude = $('input[name="long[]"]').map(function() {
                                return $(this).val();
                            }).get();
                            let latitude = $('input[name="lat[]"]').map(function() {
                                return $(this).val();
                            }).get();

                            var form_data = new FormData();
                            form_data.append('id_proposal',id_proposal);
                            form_data.append('id_pekebun',id_pekebun);
                            form_data.append('nama_tertera_dokumen_legalitas_lahan',nama_tertera_dokumen_legalitas_lahan);
                            form_data.append('nomor_dokumen_legalitas_lahan',nomor_dokumen_legalitas_lahan);
                            form_data.append('jenis_dokumen_legalitas_lahan',jenis_dokumen_legalitas_lahan);
                            form_data.append('tanggal_terbit_dokumen_legalitas_lahan',tanggal_terbit_dokumen_legalitas_lahan);
                            form_data.append('file_dokumen_legalitas_lahan',file_dokumen_legalitas_lahan);
                            form_data.append('file_shm_beda_nama',file_shm_beda_nama);
                            form_data.append('longitude',longitude);
                            form_data.append('latitude',latitude);

                            $('p.text-danger').hide().html('');

                            $.ajax({
                                url: "{{ route('tahap4.detailpekebunsave',['id_proposal'=>':id_proposal','id_legalitas'=>':id_legalitas']) }}"
                                    .replace(':id_legalitas',id_legalitas).replace(':id_proposal',id_proposal),
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
                                        text: 'Data titik koordinat Anda berhasil disimpan.',
                                        confirmButtonText: 'Ke Tambah Pengajuan',
                                    }).then((result) => {
                                        if (window.opener) {
                                            var response = {
                                                data: {
                                                    id_pekebun: id_pekebun,
                                                },
                                            };
                                        }
                                        if(result.isConfirmed){
                                            window.location.href = "{{ route('tahap4.detail',['id_proposal'=>':id_proposal']) }}".replace(':id_proposal',id_proposal);
                                        }else{
                                            window.close();
                                        }
                                    });
                                },error: function(xhr, status, error) {
                                    $('#pernyataan_luas_lahan_sesuai').prop('checked',false);
                                    $('#btn_simpan').prop('disabled',true);
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
                        } else {
                            resolve();
                            Swal.fire({
                                icon: 'error',
                                title: 'Informasi koordinat lahan masih kosong',
                                html: 'Informasi koordinat lahan Anda belum dicantumkan.<br> Silakan isi titik koordinat lahan Anda pada kolom input yang tersedia.',
                            }).then(function(){
                                $('#pernyataan_luas_lahan_sesuai').prop('checked',false);
                                $('#btn_simpan').prop('disabled',true);
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                            });
                        }
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

        $('#jenis_dokumen_legalitas_lahan').on('change',function(e){
            var target = $(this);
            if(target.val() == 'SHM'){
                $('#toggle_file_shm_beda_nama').show();
            } else {
                $('#toggle_file_shm_beda_nama').hide();
            }
        });

        map.addLayer(layer_hgu);
        map.addLayer(layer_kawasan_hutan_konservasi);
        map.addLayer(layer_kawasan_hutan_lindung);

        let legalitas_lahan_pekebun_proposal = @json($legalitas_lahan_pekebun_proposal);
        let popup_legalitas_lahan_pekebun_proposal = @json($popup_legalitas_lahan_pekebun_proposal);
        let layer_lahan_proposal = {};
        let popup_lahan_proposal = [];
        $.each(legalitas_lahan_pekebun_proposal, function(nomor_proposal,lahan_proposal){
            popup_lahan_proposal = [];
            $.each(lahan_proposal, function(index_pekebun,lahan_pekebun){
                popup_lahan_proposal.push(`
                    <strong>Nama Pekebun : </strong>${popup_legalitas_lahan_pekebun_proposal[nomor_proposal][index_pekebun].nama_pekebun}<br>
                    <strong>No / Legalitas Lahan : </strong>${popup_legalitas_lahan_pekebun_proposal[nomor_proposal][index_pekebun].nomor_dokumen_legalitas_lahan} / ${popup_legalitas_lahan_pekebun_proposal[nomor_proposal][index_pekebun].nama_tertera_dokumen_legalitas_lahan}<br>
                    <strong>Luas Lahan (Hektar) : </strong>${popup_legalitas_lahan_pekebun_proposal[nomor_proposal][index_pekebun].luas_polygon_legalitas_lahan} Ha<br>
                `);
            });
            layer_lahan_proposal[nomor_proposal] = buat_layer_group_polygon(lahan_proposal,opt_lahan_proposal,popup_lahan_proposal);
            map.addLayer(layer_lahan_proposal[nomor_proposal]);
        });
        draw_preview_tikor();

    });

    // Init peta
    // Kawasan Sawit
    const opt_lahan_proposal = {color: 'yellow',fillColor: 'yellow'};
    let layer_lahan_proposal = null;

    // HGU
    const opt_polygon_hgu = {color: 'red',fillColor: 'red'};
    let polygon_hgu = [
        [[3.914366,98.197695],[3.900744,98.214481],[3.892722,98.214489],[3.880703,98.231273],[3.868675,98.237674],[3.8278,98.283236],[3.816573,98.288836],[3.806155,98.302422],[3.798921,98.286454],[3.806932,98.272072],[3.814136,98.253696],[3.809309,98.238525],[3.806892,98.226547],[3.797258,98.219368],[3.796449,98.211382],[3.790822,98.199408],[3.78359,98.186636],[3.787581,98.165868],[3.790781,98.156281],[3.790768,98.143503],[3.780344,98.148305],[3.78835,98.132324],[3.810792,98.111537],[3.818826,98.12271],[3.82686,98.134682],[3.822067,98.155452],[3.826084,98.161039],[3.832501,98.160234],[3.840519,98.155434],[3.847733,98.149037],[3.859779,98.161804],[3.884658,98.171365],[3.895091,98.175348],[3.895091,98.175348],[3.914366,98.197695]],
        [[3.956051,98.164904],[3.947229,98.168108],[3.935204,98.176108],[3.92798,98.17292],[3.933589,98.165725],[3.948023,98.15932],[3.95444,98.159314],[3.95444,98.159314],[3.956051,98.164904]],
        [[3.769905,98.137933],[3.7651,98.146722],[3.758673,98.137944],[3.769099,98.134739],[3.769099,98.134739],[3.769905,98.137933]],
    ];
    let layer_hgu = buat_layer_group_polygon(polygon_hgu,opt_polygon_hgu);

    // Kawasan Hutan (Konservasi)
    const opt_polygon_kawasan_hutan_konservasi = {color: 'red',fillColor: 'red'};
    let polygon_kawasan_hutan_konservasi = [
        [[4.091307,97.589706],[4.109485,97.625751],[4.138281,97.654332],[4.174903,97.675093],[4.208907,97.693256],[4.211564,97.719282],[4.211615,97.750519],[4.211661,97.779153],[4.219513,97.78695],[4.237299,97.805243],[4.227402,97.818176],[4.164052,97.833871],[4.167383,97.878328],[4.117325,97.899002],[4.069421,97.903405],[4.052025,97.921862],[4.018318,97.958772],[3.968245,97.970761],[3.945391,97.981632],[3.954141,98.013063],[3.963963,98.031484],[3.973769,98.036894],[3.989012,98.035792],[3.997715,98.028191],[4.012958,98.027089],[4.023858,98.036835],[4.034772,98.057425],[4.023906,98.07804],[4.005402,98.084566],[3.982535,98.084592],[3.969467,98.083522],[3.952043,98.082457],[3.950974,98.10089],[3.951,98.124743],[3.93686,98.141022],[3.906372,98.143221],[3.887861,98.14324],[3.868264,98.147596],[3.848653,98.137859],[3.831214,98.121615],[3.819224,98.110786],[3.795258,98.101054],[3.770199,98.088072],[3.750584,98.075084],[3.71899,98.059942],[3.700479,98.061046],[3.671083,98.065412],[3.638442,98.091458],[3.614495,98.101237],[3.590551,98.113182],[3.554624,98.121887],[3.524136,98.124083],[3.470791,98.13822],[3.428325,98.141509],[3.372795,98.149143],[3.327064,98.1546],[3.307463,98.154617],[3.292209,98.144878],[3.29218,98.112374],[3.277989,98.073383],[3.296457,98.027861],[3.321457,97.983415],[3.333409,97.958483],[3.359513,97.931368],[3.385629,97.91617],[3.423731,97.908543],[3.46291,97.891161],[3.484671,97.877049],[3.547769,97.832543],[3.57714,97.810832],[3.589099,97.796729],[3.66204,97.789046],[3.701225,97.780322],[3.721851,97.73694],[3.713058,97.680595],[3.756529,97.628506],[3.792389,97.586179],[3.810885,97.578562],[3.853311,97.557898],[3.909939,97.566474],[3.950266,97.592422],[3.984062,97.619467],[4.023201,97.599874],[4.091307,97.589706]],
    ];
    let layer_kawasan_hutan_konservasi = buat_layer_group_polygon(polygon_kawasan_hutan_konservasi,opt_polygon_kawasan_hutan_konservasi);

    // Kawasan Hutan (Lindung)
    const opt_polygon_kawasan_hutan_lindung = {color: 'red',fillColor: 'red'};
    let polygon_kawasan_hutan_lindung = [
        [[4.237299,97.805243],[4.219513,97.78695],[4.211661,97.779153],[4.211615,97.750519],[4.211564,97.719282],[4.208907,97.693256],[4.174903,97.675093],[4.138281,97.654332],[4.109485,97.625751],[4.091307,97.589706],[4.093742,97.589343],[4.179961,97.578779],[4.352361,97.539412],[4.373517,97.674754],[4.261308,97.773868],[4.237299,97.805243]],
    ];
    let layer_kawasan_hutan_lindung = buat_layer_group_polygon(polygon_kawasan_hutan_lindung,opt_polygon_kawasan_hutan_lindung);
</script>
