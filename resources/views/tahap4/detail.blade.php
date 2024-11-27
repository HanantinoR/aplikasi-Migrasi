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
                                    <h6 class="font-weight-semibold text-lg mb-0">TAHAP 4 -- <strong>Rekonsiliasi Titik Koordinat Proposal</strong></h6>
                                    <p class="text-sm mb-sm-0"><strong>{{ $get_data_kelembagaan_pekebun->nomor_proposal }} --
                                        {{ $get_data_kelembagaan_pekebun->nama_kelembagaan_pekebun }}</strong>
                                    </p>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card  shadow-xs mb-4">

                        <div class="card-body border-1 mt-4">
                            <div class="row">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <h3>Peta Sebaran Pekebun Kelembagaan</h3>
                                                    <h5>Jumlah Pekebun : {{ count($data_pekebun_sudah_rekon) }} Pekebun</h5>
                                                    @php
                                                        $luas_lahan_rekon = $data_pekebun_sudah_rekon->sum('luas_lahan');
                                                    @endphp
                                                    <h5>Luas Lahan : {{ number_format($luas_lahan_rekon, 4, '.', ',') }} Hektar
                                                    </h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div id="map" style="height: 70vh; width:auto;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="accordion" id="accordionGeneralLedger">
                                        <div class="accordion-item mb-3">
                                            {{-- <a href="https://program-psr.bpdp.or.id/assets/upload_file/proposal/{{ @$get_data_kelembagaan_pekebun->sk_penetapan_dirut }}"
                                                target="blank"
                                                class="btn btn-sm btn-primary btn-icon align-items-center mb-0 me-2">
                                                <i class="fa-solid fa-link fa-2xl"></i>
                                                <span class="btn-inner--text"> Klik Disini Untuk Melihat SK Dirut</span>
                                            </a> --}}

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
                                                                        Zoom In Peta</th>
                                                                    <th
                                                                        class="text-secondary text-xs font-weight-semibold opacity-7">
                                                                        Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $i = 1;
                                                                @endphp
                                                                @foreach ($data_pekebun_sudah_rekon as $data_pekebun)
                                                                    <tr>
                                                                        <td>{{ $i++ }}</td>
                                                                        <td>{{ $data_pekebun->nama_pekebun }}</td>
                                                                        <td>{{ $data_pekebun->nik_pekebun }}</td>
                                                                        <td>{{ number_format($data_pekebun->luas_lahan, 4, '.', ',') }}
                                                                        </td>
                                                                        <td>
                                                                            @if ($data_pekebun->tikor_zoom === "Tidak Bisa Zoom")
                                                                                <button type="button" class="btn btn-danger btn-icon align-items-center mb-0 me-2"><i class="fa-solid fa-check-to-slot"></i> !!!Tidak Ada Titik Koordinat!!!</button>
                                                                            @else
                                                                                <button type="button" onclick="map.setView([{{$data_pekebun->tikor_zoom}}],16)" class="btn btn-success btn-icon align-items-center mb-0 me-2"><i class="fa-solid fa-check-to-slot"></i> Zoom In</button>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{-- <a href='{{route('tahap2.dokumen_pekebun',[])}}' type="button" class="btn btn-info m-0"><i class="fa-solid fa-file-circle-check"></i> Rekonsiliasi Dokumen Pekebun!</a> --}}
                                                                            <br>
                                                                            <a href='' type="button" class="btn btn-success btn-icon align-items-center mb-0 me-2"><i class="fa-solid fa-check-to-slot"></i> Rekonsiliasi Legalitas Lahan Pekebun!</a>
                                                                        </td>
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
</x-app-layout>
<script>
    $(document).ready(function() {

        $('#tableGeneralLedger').DataTable({
            paging: false
        });

    });
</script>
<script>
    var map = L.map('map',{
        zoomControl: false,
    }).fitWorld()
    // .setView([1.0022060429846, 101.315047132], 15);
    .setView([-2.6000285, 118.015776], 5);

    var mapLink = '<a href="https://www.esri.com/">Esri</a>';
    var wholink =
        'i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';

    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; ' + mapLink + ', ' + wholink,
            maxZoom: 18,
        }).addTo(map);
    // var polygon4 = L.polygon([
    //     [1.002828388405, 101.31951172265],
    //     [1.0027240384633, 101.31949721717],
    //     [1.0018285423269, 101.31917755272],
    //     [1.0020197878418, 101.31824441652],
    //     [1.0020806147315, 101.31826267758],
    //     [1.0027485743436, 101.31846320641],
    //     [1.0029690755786, 101.31852940311],
    //     [1.002828388405, 101.31951172265]
    // ]).bindPopup(
    //     '<strong> Nama Lembaga Pekebun : </strong>KOP PROD BAKTI NUSANTARA LIMA ENAM <br> <strong> Nama Pekebun : </strong>AHMAD JUNAIDI<br> <strong>No / Legalitas Lahan : </strong>00.00.00.00.0.00105 / MUHRONI<br> <strong>Lokasi Kebun : </strong>Muaro Jambi <br> <strong>Luas Lahan (Hektar) : </strong>4 Hektar <br> <strong>Umur Tanaman : </strong>35 Tahun <br> <strong>Produktifitas : </strong> > 10 ton/Ha/tahun <a class="btn btn-sm bg-success" href="/mappekebun"><i class="fas fa-search"></i>Klik Untuk Melihat Perubahan Lahan</a>'
    // );

    // HGU
    const opt_polygon_hgu = {color: 'pink',fillColor: 'pink'};
    let polygon_hgu = [
        L.polygon([[3.914366,98.197695],[3.900744,98.214481],[3.892722,98.214489],[3.880703,98.231273],[3.868675,98.237674],[3.8278,98.283236],[3.816573,98.288836],[3.806155,98.302422],[3.798921,98.286454],[3.806932,98.272072],[3.814136,98.253696],[3.809309,98.238525],[3.806892,98.226547],[3.797258,98.219368],[3.796449,98.211382],[3.790822,98.199408],[3.78359,98.186636],[3.787581,98.165868],[3.790781,98.156281],[3.790768,98.143503],[3.780344,98.148305],[3.78835,98.132324],[3.810792,98.111537],[3.818826,98.12271],[3.82686,98.134682],[3.822067,98.155452],[3.826084,98.161039],[3.832501,98.160234],[3.840519,98.155434],[3.847733,98.149037],[3.859779,98.161804],[3.884658,98.171365],[3.895091,98.175348],[3.895091,98.175348],[3.914366,98.197695]],opt_polygon_hgu),
        L.polygon([[3.956051,98.164904],[3.947229,98.168108],[3.935204,98.176108],[3.92798,98.17292],[3.933589,98.165725],[3.948023,98.15932],[3.95444,98.159314],[3.95444,98.159314],[3.956051,98.164904]],opt_polygon_hgu),
        L.polygon([[3.769905,98.137933],[3.7651,98.146722],[3.758673,98.137944],[3.769099,98.134739],[3.769099,98.134739],[3.769905,98.137933]],opt_polygon_hgu),
    ];
    let layer_hgu = L.layerGroup(polygon_hgu);

    // Kawasan Hutan (Konservasi)
    const opt_polygon_kawasan_hutan_konservasi = {color: 'orange',fillColor: 'orange'};
    let polygon_kawasan_hutan_konservasi = [
        L.polygon([[4.091307,97.589706],[4.109485,97.625751],[4.138281,97.654332],[4.174903,97.675093],[4.208907,97.693256],[4.211564,97.719282],[4.211615,97.750519],[4.211661,97.779153],[4.219513,97.78695],[4.237299,97.805243],[4.227402,97.818176],[4.164052,97.833871],[4.167383,97.878328],[4.117325,97.899002],[4.069421,97.903405],[4.052025,97.921862],[4.018318,97.958772],[3.968245,97.970761],[3.945391,97.981632],[3.954141,98.013063],[3.963963,98.031484],[3.973769,98.036894],[3.989012,98.035792],[3.997715,98.028191],[4.012958,98.027089],[4.023858,98.036835],[4.034772,98.057425],[4.023906,98.07804],[4.005402,98.084566],[3.982535,98.084592],[3.969467,98.083522],[3.952043,98.082457],[3.950974,98.10089],[3.951,98.124743],[3.93686,98.141022],[3.906372,98.143221],[3.887861,98.14324],[3.868264,98.147596],[3.848653,98.137859],[3.831214,98.121615],[3.819224,98.110786],[3.795258,98.101054],[3.770199,98.088072],[3.750584,98.075084],[3.71899,98.059942],[3.700479,98.061046],[3.671083,98.065412],[3.638442,98.091458],[3.614495,98.101237],[3.590551,98.113182],[3.554624,98.121887],[3.524136,98.124083],[3.470791,98.13822],[3.428325,98.141509],[3.372795,98.149143],[3.327064,98.1546],[3.307463,98.154617],[3.292209,98.144878],[3.29218,98.112374],[3.277989,98.073383],[3.296457,98.027861],[3.321457,97.983415],[3.333409,97.958483],[3.359513,97.931368],[3.385629,97.91617],[3.423731,97.908543],[3.46291,97.891161],[3.484671,97.877049],[3.547769,97.832543],[3.57714,97.810832],[3.589099,97.796729],[3.66204,97.789046],[3.701225,97.780322],[3.721851,97.73694],[3.713058,97.680595],[3.756529,97.628506],[3.792389,97.586179],[3.810885,97.578562],[3.853311,97.557898],[3.909939,97.566474],[3.950266,97.592422],[3.984062,97.619467],[4.023201,97.599874],[4.091307,97.589706]],opt_polygon_kawasan_hutan_konservasi),
    ];
    let layer_kawasan_hutan_konservasi = L.layerGroup(polygon_kawasan_hutan_konservasi);

    // Kawasan Hutan (Lindung)
    const opt_polygon_kawasan_hutan_lindung = {color: 'red',fillColor: 'red'};
    let polygon_kawasan_hutan_lindung = [
        L.polygon([[4.237299,97.805243],[4.219513,97.78695],[4.211661,97.779153],[4.211615,97.750519],[4.211564,97.719282],[4.208907,97.693256],[4.174903,97.675093],[4.138281,97.654332],[4.109485,97.625751],[4.091307,97.589706],[4.093742,97.589343],[4.179961,97.578779],[4.352361,97.539412],[4.373517,97.674754],[4.261308,97.773868],[4.237299,97.805243]],opt_polygon_kawasan_hutan_lindung),
    ];
    let layer_kawasan_hutan_lindung = L.layerGroup(polygon_kawasan_hutan_lindung);

    // function onMapClick(e) {
    //     console.log(e.latlng);
    // }
    // map.on('click', onMapClick);

    // Create custom map control
    L.control.zoom({
        position: 'topright'
    }).addTo(map);
    map.addControl(new control_peta());

    $(document).ready(function() {
        let layer_peta_toggle = document.getElementsByClassName('layer-peta-toggle')[0];
        L.DomEvent.on(layer_peta_toggle, 'click', function() {
            $('.layer-peta').toggleClass('minimize-layer-peta');
        });

        // Lahan Proposal Akun
        const opt_lahan_proposal = {color: '#FFFF00',fillColor: '#FFFF00'};
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
        tampilkan_kategori_layer('Lahan Proposal',layer_lahan_proposal,opt_lahan_proposal.color);

        // Lahan Proposal Akun Lain
        // const opt_lahan_proposal_lain = {color: 'red',fillColor: 'red'};
        // let legalitas_lahan_pekebun_proposal_lain = @json($legalitas_lahan_pekebun_proposal_lain);

        // let layer_lahan_proposal_lain = buat_layer_group_polygon(legalitas_lahan_pekebun_proposal_lain,opt_lahan_proposal_lain);
        // map.addLayer(layer_lahan_proposal_lain);
        // tampilkan_layer('Lahan Proposal Lain',layer_lahan_proposal_lain,opt_lahan_proposal_lain.color);

        // map.addLayer(layer_placeholder_1);
        // map.addLayer(layer_placeholder_2);
        map.addLayer(layer_hgu);
        map.addLayer(layer_kawasan_hutan_konservasi);
        map.addLayer(layer_kawasan_hutan_lindung);

        // tampilkan_layer('Kawasan Sawit',layer_kawasan_sawit);
        tampilkan_layer('Hak Guna Usaha (HGU)',layer_hgu);
        tampilkan_kategori_layer('Kawasan Hutan',{
            'Kawasan Hutan (Konservasi)':layer_kawasan_hutan_konservasi,
            'Kawasan Hutan (Lindung)':layer_kawasan_hutan_lindung,
        });
        // tampilkan_kategori_layer('Layer Placeholder',{
        //     'Placeholder 1':layer_placeholder_1,
        //     'Placeholder 2':layer_placeholder_2,
        // });
    });
</script>
