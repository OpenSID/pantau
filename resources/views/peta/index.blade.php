@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <style>
        #map {
            width: 100%;
            height: 60vh;
        }
    </style>
@endpush
@section('title', 'Online Pengguna OpenSID')

@section('content_header')
    <h1>Online Pengguna OpenSID<small class="font-weight-light ml-1 text-md">Pengguna aktif dalam 7 hari terakhir</small>
    </h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-3">
                    <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                        aria-expanded="false" aria-controls="collapse-filter">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="collapse-filter" class="collapse">
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select class="select2 form-control-sm" id="provinsi" name="provinsi"
                                        data-placeholder="Pilih Provinsi" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Kabupaten</label>
                                    <select class="select2 form-control-sm" id="kabupaten" name="kabupaten"
                                        data-placeholder="Pilih Kabupaten" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select class="select2 form-control-sm" id="kecamatan" name="kecamatan"
                                        data-placeholder="Pilih Kecamatan" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Online</label>
                                    <select class="select2 form-control-sm" id="status" name="Online"
                                        data-placeholder="Pilih Status" style="width: 100%;">
                                        <option value="">Semua</option>
                                        <option value="1">Online</option>
                                        <option value="2">Offline</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="btn-group btn-group-sm btn-block">
                                            <button type="button" id="reset" class="btn btn-secondary"><span
                                                    class="fas fa-ban"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="btn-group btn-group-sm btn-block">
                                            <button type="button" id="filter" class="btn btn-primary"><span
                                                    class="fas fa-search"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-0">
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="map"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    <script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
    {{-- <script src="https://leafletjs.com/examples/geojson/sample-geojson.js"></script> --}}
    <script>
        $(document).ready(function() {
            var markersBar;
            var barLayer;

            var map = L.map('map').setView([{{ config('leaflet.map_center_latitude') }},
                {{ config('leaflet.map_center_longitude') }}
            ], {{ config('leaflet.zoom_level') }});

            var mbAttr =
                'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
            var mbUrl =
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

            var streets = L.tileLayer(mbUrl, {
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                attribution: mbAttr
            });

            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var baseLayers = {
                'OpenStreetMap': osm,
                'Streets': streets
            };

            // Tambahkan jenis map
            var layerControl = L.control.layers(baseLayers).addTo(map);

            var satellite = L.tileLayer(mbUrl, {
                id: 'mapbox/satellite-v9',
                tileSize: 512,
                zoomOffset: -1,
                attribution: mbAttr
            });
            layerControl.addBaseLayer(satellite, 'Satellite');

            // Ubah icon
            var baseballIcon = L.icon({
                iconUrl: "{{ url('assets/img/opensid_logo.png') }}",
                iconSize: [20, 20],
            });

            function onEachFeature(feature, layer) {
                layer.bindPopup(feature.properties.popupContent);
            }

            loadData();

            $('#filter').click(function() {
                // Kosongkan Map Telebih Dahulu
                map.removeLayer(markersBar);
                loadData($('#provinsi').val(), $('#kabupaten').val(), $('#kecamatan').val(), $('#status')
                    .val());
            });

            function loadData(kode_provinsi = null, kode_kabupaten = null, kode_kecamatan = null, status = null) {

                $.ajax({
                    url: "{{ url('peta/desa') }}",
                    contentType: "application/json; charset=utf-8",
                    cache: false,
                    dataType: "json",
                    data: {
                        kode_provinsi: kode_provinsi,
                        kode_kabupaten: kode_kabupaten,
                        kode_kecamatan: kode_kecamatan,
                        status: status,
                    },
                    responseType: "json",
                    success: function(response) {

                        // Buat Marker Cluster Group 
                        markersBar = L.markerClusterGroup();

                        // Simpan Data geoJSON
                        barLayer = new L.geoJSON(response, {
                            pointToLayer: function(feature, latlng) {
                                return L.marker(latlng, {
                                    icon: baseballIcon
                                });
                            },

                            onEachFeature: onEachFeature
                        });

                        // Tambahkan Marker dan Marker Cluster Group pada Map
                        markersBar.addLayer(barLayer);
                        map.addLayer(markersBar);
                    },
                    error: function() {
                        alert('Gagal mengambil data');
                    },
                });
            }

            function markerDelAgain() {
                for (i = 0; i < marker.length; i++) {
                    map.removeLayer(marker[i]);
                }
            }
        });
    </script>
@endsection
