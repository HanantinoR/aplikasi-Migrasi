<!--
=========================================================
* Corporate UI - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/corporate-ui
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('/assets/img/apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('/assets/img/favicon.png')}}">
    <title>
        Rekon Data PSR
    </title>
    <!--     Fonts and icons     -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700"
        rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Nucleo Icons -->
    <link href="{{asset('/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script> --}}
    <link href="{{asset('/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{asset('/assets/css/corporate-ui-dashboard.css?v=1.0.0')}}" rel="stylesheet" />
    <!-- datatables-->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        /* Style Peta */
        .layer-peta{
            margin-right: 10px;
        }
        .layer-peta-toggle{
            border: none;
            padding: 5px 10px;
        }
        .minimize-layer-peta{
            transform: translateX(-110%);
        }
        .minimize-layer-peta .layer-peta-toggle{
            transform: translateX(6.5em);
            color: white !important;
            border: 1px solid white;
            border-radius: 0.5em;
            padding: 10px 15px;
        }
    </style>

</head>

<body class="g-sidenav-show  bg-gray-100">
    @php
        $topSidenavArray = ['wallet', 'profile'];
        $topSidenavTransparent = ['signin', 'signup'];
        $topSidenavRTL = ['RTL'];
    @endphp
    @if (in_array(request()->route()->getName(),
            $topSidenavArray))
        <x-sidenav-top />
    @elseif(in_array(request()->route()->getName(),
            $topSidenavTransparent))

    @elseif(in_array(request()->route()->getName(),
            $topSidenavRTL))
    @else
        <x-app.sidebar />
    @endif

    {{ $slot }}

    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="fa fa-cog py-2"></i>
        </a>
        <div class="card shadow-lg ">
            <div class="card-header pb-0 pt-3 ">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">Corporate UI Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger"
                            onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-slate-900"
                        onclick="sidebarType(this)">Dark</button>
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white"
                        onclick="sidebarType(this)">White</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="mt-3">
                    <h6 class="mb-0">Navbar Fixed</h6>
                </div>
                <div class="form-check form-switch ps-0">
                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                        onclick="navbarFixed(this)">
                </div>
                <hr class="horizontal dark my-sm-4">
                <a class="btn bg-gradient-dark w-100" target="_blank"
                    href="https://www.creative-tim.com/product/corporate-ui-dashboard-laravel">Free Download</a>
                <a class="btn btn-outline-dark w-100" target="_blank"
                    href="https://www.creative-tim.com/learning-lab/bootstrap/installation-guide/corporate-ui-dashboard">View
                    documentation</a>
                <div class="w-100 text-center">
                    <a class="github-button" target="_blank" href="https://github.com/creativetimofficial/corporate-ui-dashboard-laravel"
                        data-icon="octicon-star" data-size="large" data-show-count="true"
                        aria-label="Star creativetimofficial/corporate-ui-dashboard on GitHub">Star</a>
                    <h6 class="mt-3">Thank you for sharing!</h6>
                    <a href="https://twitter.com/intent/tweet?text=Check%20Corporate%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%26%20%40UPDIVISION%20%23webdesign%20%23dashboard%20%23bootstrap5%20%23laravel&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fcorporate-ui-dashboard-laravel"
                    class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/corporate-ui-dashboard-laravel"
                        class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{asset('/assets/js/core/popper.min.js')}}"></script>
    <script src="{{asset('/assets/js/core/bootstrap.min.js')}}"></script>
    <script src="{{asset('/assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('/assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{asset('/assets/js/plugins/chartjs.min.js')}}"></script>
    <script src="{{asset('/assets/js/plugins/swiper-bundle.min.js')}}" type="text/javascript"></script>
    <script>
        if (document.getElementsByClassName('mySwiper')) {
            var swiper = new Swiper(".mySwiper", {
                effect: "cards",
                grabCursor: true,
                initialSlide: 1,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        };


        // var ctx = document.getElementById("chart-bars").getContext("2d");

        // new Chart(ctx, {
        //     type: "bar",
        //     data: {
        //         labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
        //         datasets: [{
        //                 label: "Sales",
        //                 tension: 0.4,
        //                 borderWidth: 0,
        //                 borderSkipped: false,
        //                 backgroundColor: "#2ca8ff",
        //                 data: [450, 200, 100, 220, 500, 100, 400, 230, 500, 200],
        //                 maxBarThickness: 6
        //             },
        //             {
        //                 label: "Sales",
        //                 tension: 0.4,
        //                 borderWidth: 0,
        //                 borderSkipped: false,
        //                 backgroundColor: "#7c3aed",
        //                 data: [200, 300, 200, 420, 400, 200, 300, 430, 400, 300],
        //                 maxBarThickness: 6
        //             },
        //         ],
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         plugins: {
        //             legend: {
        //                 display: false,
        //             },
        //             tooltip: {
        //                 backgroundColor: '#fff',
        //                 titleColor: '#1e293b',
        //                 bodyColor: '#1e293b',
        //                 borderColor: '#e9ecef',
        //                 borderWidth: 1,
        //                 usePointStyle: true
        //             }
        //         },
        //         interaction: {
        //             intersect: false,
        //             mode: 'index',
        //         },
        //         scales: {
        //             y: {
        //                 stacked: true,
        //                 grid: {
        //                     drawBorder: false,
        //                     display: true,
        //                     drawOnChartArea: true,
        //                     drawTicks: false,
        //                     borderDash: [4, 4],
        //                 },
        //                 ticks: {
        //                     beginAtZero: true,
        //                     padding: 10,
        //                     font: {
        //                         size: 12,
        //                         family: "Noto Sans",
        //                         style: 'normal',
        //                         lineHeight: 2
        //                     },
        //                     color: "#64748B"
        //                 },
        //             },
        //             x: {
        //                 stacked: true,
        //                 grid: {
        //                     drawBorder: false,
        //                     display: false,
        //                     drawOnChartArea: false,
        //                     drawTicks: false
        //                 },
        //                 ticks: {
        //                     font: {
        //                         size: 12,
        //                         family: "Noto Sans",
        //                         style: 'normal',
        //                         lineHeight: 2
        //                     },
        //                     color: "#64748B"
        //                 },
        //             },
        //         },
        //     },
        // });


        // var ctx2 = document.getElementById("chart-line").getContext("2d");

        // var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

        // gradientStroke1.addColorStop(1, 'rgba(45,168,255,0.2)');
        // gradientStroke1.addColorStop(0.2, 'rgba(45,168,255,0.0)');
        // gradientStroke1.addColorStop(0, 'rgba(45,168,255,0)'); //blue colors

        // var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

        // gradientStroke2.addColorStop(1, 'rgba(119,77,211,0.4)');
        // gradientStroke2.addColorStop(0.7, 'rgba(119,77,211,0.1)');
        // gradientStroke2.addColorStop(0, 'rgba(119,77,211,0)'); //purple colors

        // new Chart(ctx2, {
        //     plugins: [{
        //         beforeInit(chart) {
        //             const originalFit = chart.legend.fit;
        //             chart.legend.fit = function fit() {
        //                 originalFit.bind(chart.legend)();
        //                 this.height += 40;
        //             }
        //         },
        //     }],
        //     type: "line",
        //     data: {
        //         labels: ["Aug 18", "Aug 19", "Aug 20", "Aug 21", "Aug 22", "Aug 23", "Aug 24", "Aug 25", "Aug 26",
        //             "Aug 27", "Aug 28", "Aug 29", "Aug 30", "Aug 31", "Sept 01", "Sept 02", "Sept 03", "Aug 22",
        //             "Sept 04", "Sept 05", "Sept 06", "Sept 07", "Sept 08", "Sept 09"
        //         ],
        //         datasets: [{
        //                 label: "Volume",
        //                 tension: 0,
        //                 borderWidth: 2,
        //                 pointRadius: 3,
        //                 borderColor: "#2ca8ff",
        //                 pointBorderColor: '#2ca8ff',
        //                 pointBackgroundColor: '#2ca8ff',
        //                 backgroundColor: gradientStroke1,
        //                 fill: true,
        //                 data: [2828, 1291, 3360, 3223, 1630, 980, 2059, 3092, 1831, 1842, 1902, 1478, 1123,
        //                     2444, 2636, 2593, 2885, 1764, 898, 1356, 2573, 3382, 2858, 4228
        //                 ],
        //                 maxBarThickness: 6

        //             },
        //             {
        //                 label: "Trade",
        //                 tension: 0,
        //                 borderWidth: 2,
        //                 pointRadius: 3,
        //                 borderColor: "#832bf9",
        //                 pointBorderColor: '#832bf9',
        //                 pointBackgroundColor: '#832bf9',
        //                 backgroundColor: gradientStroke2,
        //                 fill: true,
        //                 data: [2797, 2182, 1069, 2098, 3309, 3881, 2059, 3239, 6215, 2185, 2115, 5430, 4648,
        //                     2444, 2161, 3018, 1153, 1068, 2192, 1152, 2129, 1396, 2067, 1215, 712, 2462,
        //                     1669, 2360, 2787, 861
        //                 ],
        //                 maxBarThickness: 6
        //             },
        //         ],
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         plugins: {
        //             legend: {
        //                 display: true,
        //                 position: 'top',
        //                 align: 'end',
        //                 labels: {
        //                     boxWidth: 6,
        //                     boxHeight: 6,
        //                     padding: 20,
        //                     pointStyle: 'circle',
        //                     borderRadius: 50,
        //                     usePointStyle: true,
        //                     font: {
        //                         weight: 400,
        //                     },
        //                 },
        //             },
        //             tooltip: {
        //                 backgroundColor: '#fff',
        //                 titleColor: '#1e293b',
        //                 bodyColor: '#1e293b',
        //                 borderColor: '#e9ecef',
        //                 borderWidth: 1,
        //                 pointRadius: 2,
        //                 usePointStyle: true,
        //                 boxWidth: 8,
        //             }
        //         },
        //         interaction: {
        //             intersect: false,
        //             mode: 'index',
        //         },
        //         scales: {
        //             y: {
        //                 grid: {
        //                     drawBorder: false,
        //                     display: true,
        //                     drawOnChartArea: true,
        //                     drawTicks: false,
        //                     borderDash: [4, 4]
        //                 },
        //                 ticks: {
        //                     callback: function(value, index, ticks) {
        //                         return parseInt(value).toLocaleString() + ' EUR';
        //                     },
        //                     display: true,
        //                     padding: 10,
        //                     color: '#b2b9bf',
        //                     font: {
        //                         size: 12,
        //                         family: "Noto Sans",
        //                         style: 'normal',
        //                         lineHeight: 2
        //                     },
        //                     color: "#64748B"
        //                 }
        //             },
        //             x: {
        //                 grid: {
        //                     drawBorder: false,
        //                     display: false,
        //                     drawOnChartArea: false,
        //                     drawTicks: false,
        //                     borderDash: [4, 4]
        //                 },
        //                 ticks: {
        //                     display: true,
        //                     color: '#b2b9bf',
        //                     padding: 20,
        //                     font: {
        //                         size: 12,
        //                         family: "Noto Sans",
        //                         style: 'normal',
        //                         lineHeight: 2
        //                     },
        //                     color: "#64748B"
        //                 }
        //             },
        //         },
        //     },
        // });
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Corporate UI Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{asset('/assets/js/corporate-ui-dashboard.min.js?v=1.0.0')}}"></script>
    {{-- Peta Dibawah Sini --}}
    <script>
        // Untuk layer control peta
        let control_peta = L.Control.extend({
            options: {
                position: 'topleft',
            },
            onAdd: function(map) {
                var container = L.DomUtil.create('div', 'navbar-vertical layer-peta minimize-layer-peta');
                let map_width = $('#map').width();
                let map_height = $('#map').height();
                container.innerHTML = `
                    <div class="card" style="max-width:${map_width-20}px;max-height:${map_height-20}px;">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col">
                                    <h4>Layer</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="layer-peta-toggle text-lg text-secondary bg-transparent mb-0">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mx-2">
                        <div class="card-body pt-0" style="overflow-y:auto;">
                            <ul class="navbar-nav">
                            </ul>
                        </div>
                    </div>
                `;
                L.DomEvent.disableScrollPropagation(container);
                L.DomEvent.disableClickPropagation(container);

                return container;
            },
        });

        function tampilkan_kategori_layer(nama_kategori_layer,data_kategori_layer,opt) {
            let id_kategori_layer = nama_kategori_layer.toLowerCase().replaceAll(' ','_').replaceAll(/[<>:"\/\\|()?*]/g,'');
            let layer = '';
            if(Object.keys(data_kategori_layer).length > 0){
                $.each(data_kategori_layer,function(nama_layer,data_layer){
                    let id_layer = nama_layer.toLowerCase().replace(' ','_');
                    let warna_layer = data_layer.getLayers().length > 0 ?
                        (
                            data_layer.getLayers()[0].options.color ?
                            data_layer.getLayers()[0].options.color : data_layer.getLayers()[0].options.style.color
                        ) : opt;
                    layer += `
                        <li class="nav-item">
                            <div class="d-flex py-2">
                                <label for="${id_layer}" class="form-check-label opacity-8 text-lg mb-0 ms-3 me-3">
                                    ${nama_layer}
                                </label>
                                <div class="form-check form-switch ps-0 ms-auto my-auto">
                                    <input type="checkbox" id="${id_layer}" class="form-check-input mt-1 ms-auto" style="height:20px;border-color:${warna_layer};background-color:${warna_layer};" checked>
                                </div>
                            </div>
                        </li>
                    `;
                });
            } else {
                layer += `
                    <li class="nav-item">
                        <div class="d-flex py-2">
                            <span class=" opacity-8 text-lg mb-0 ms-3 me-3">
                                Data Kosong
                            </span>
                        </div>
                    </li>
                `;
            }

            let kategori_layer = `
                <li class="nav-item">
                    <a href class="nav-link text-lg font-weight-bolder px-0" data-bs-toggle="collapse" data-bs-target="#${id_kategori_layer}" aria-controls="${id_kategori_layer}" aria-expanded="true">
                        ${nama_kategori_layer}
                    </a>
                    <ul class="list-unstyled collapse show" id="${id_kategori_layer}" aria-labelledby="${id_kategori_layer}">
                        ${layer}
                    </ul>
                </li>
            `;

            $('.layer-peta .card-body .navbar-nav').append(`
                ${kategori_layer}
            `);

            $.each(data_kategori_layer,function(nama_layer,data_layer){
                let id_layer = nama_layer.toLowerCase().replace(' ','_');
                let warna_layer = data_layer.getLayers().length > 0 ?
                    (
                        data_layer.getLayers()[0].options.color ?
                        data_layer.getLayers()[0].options.color : data_layer.getLayers()[0].options.style.color
                    ) : opt;
                let toggle_layer = document.getElementById(id_layer);
                L.DomEvent.on(toggle_layer, 'change', function() {
                    if (toggle_layer.checked) {
                        map.addLayer(data_layer);
                        toggle_layer.style.borderColor = warna_layer;
                        toggle_layer.style.backgroundColor = warna_layer;
                    } else {
                        map.removeLayer(data_layer);
                        toggle_layer.style.borderColor = '#e9ecef';
                        toggle_layer.style.backgroundColor = 'rgba(33, 37, 41, 0.1)';
                    }
                });
            });
        }

        function tampilkan_layer(nama_layer,data_layer,opt) {
            let id_layer = nama_layer.toLowerCase().replace(' ','_');
            let warna_layer = data_layer.getLayers().length > 0 ?
                (
                    data_layer.getLayers()[0].options.color ?
                    data_layer.getLayers()[0].options.color : data_layer.getLayers()[0].options.style.color
                ) : opt;
            $('.layer-peta .card-body .navbar-nav').append(`
                <li class="nav-item">
                    <div class="d-flex py-2">
                        <label for="${id_layer}" class="form-check-label font-weight-bolder opacity-8 text-lg mb-0 ms-0 me-3">
                            ${nama_layer}
                        </label>
                        <div class="form-check form-switch ps-0 ms-auto my-auto">
                            <input type="checkbox" id="${id_layer}" class="form-check-input mt-1 ms-auto" style="height:20px;border-color:${warna_layer};background-color:${warna_layer};" checked>
                        </div>
                    </div>
                </li>
            `);
            let toggle_layer = document.getElementById(id_layer);
            L.DomEvent.on(toggle_layer, 'change', function() {
                if (toggle_layer.checked) {
                    map.addLayer(data_layer);
                    toggle_layer.style.borderColor = warna_layer;
                    toggle_layer.style.backgroundColor = warna_layer;
                } else {
                    map.removeLayer(data_layer);
                    toggle_layer.style.borderColor = '#e9ecef';
                    toggle_layer.style.backgroundColor = 'rgba(33, 37, 41, 0.1)';
                }
            });
        }

        // Convert tikor ke format GeoJSON
        function to_geo_json(polygon, popup_body) {
            if(polygon[0][0] < 80){
                return {
                    type: "Feature",
                    properties: {"name": popup_body} || {},
                    geometry: {
                        type: "Polygon",
                        coordinates: [polygon.map(coord => [coord[1], coord[0]])]
                    }
                };
            }else{
                return {
                    type: "Feature",
                    properties: {"name": popup_body} || {},
                    geometry: {
                        type: "Polygon",
                        coordinates: [polygon.map(coord => [coord[0], coord[1]])]
                    }
                };
            }
        }

        // Bind popup ke feature, atur sesuai property
        function on_each_feature(feature, layer) {
            if (feature.properties && feature.properties.name) {
                layer.bindPopup(feature.properties.name);
            }
        }

        // Map polygon ke layer, layer ke group
        function buat_layer_group_polygon(group_polygon,opt,popup){
            let layer_group_polygon = L.layerGroup();
            let geojson_polygon = popup ? group_polygon.map((polygon,index) => to_geo_json(polygon,popup[index])) : group_polygon.map(to_geo_json);
            geojson_polygon.forEach(geojson => {
                var layer = L.geoJSON(geojson, {
                    style: opt || {color: "blue",fillColor: "blue"},
                    onEachFeature: on_each_feature,
                });
                layer_group_polygon.addLayer(layer);
            });
            return layer_group_polygon;
        }
    </script>
</body>

</html>
