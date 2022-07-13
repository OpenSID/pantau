@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
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
                                    <select class="select2 form-control-sm" id="Online" name="Online"
                                        data-placeholder="Pilih Online" style="width: 100%;">
                                    </select>
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
    <script>
        $(document).ready(function() {
            var mapCenter = [
                {{ config('leaflet.map_center_latitude') }},
                {{ config('leaflet.map_center_longitude') }}
            ];

            // {{ request('latitude', config('leaflet.map_center_latitude')) }},
            // {{ request('longitude', config('leaflet.map_center_longitude')) }}

            // var map = L.map('map').setView(mapCenter, {{ config('leaflet.zoom_level') }});

            // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            //     maxZoom: 19,
            //     attribution: '© OpenStreetMap'
            // }).addTo(map);

            // Icon
            var logo = L.icon({
                iconUrl: "{{ url('assets/img/opensid_logo.png') }}",
                iconSize: [20, 20],
            });

            // Desa Online
            var Online = L.layerGroup();
            var bali = L.marker([-8.439771599521729, 115.19934061914685], {
                icon: logo
            }).bindPopup('<br> asasas<br>').addTo(
                Online);
            var mataram = L.marker([-8.53539246061938, 116.20129354298115], {
                icon: logo
            }).bindPopup('This is Denver, CO.').addTo(
                Online);
            var malang = L.marker([-8.017891590877028, 112.69006283953787], {
                icon: logo
            }).bindPopup('This is Aurora, CO.').addTo(
                Online);
            var madura = L.marker([-7.072546448844008, 113.30969167873265], {
                icon: logo
            }).bindPopup('This is Golden, CO.').addTo(
                Online);

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
            });

            var map = L.map('map', {
                center: mapCenter,
                zoom: {{ config('leaflet.zoom_level') }},
                layers: [osm, Online]
            });

            var baseLayers = {
                'OpenStreetMap': osm,
                'Streets': streets
            };

            var overlays = {
                'Online': Online
            };

            // Desa Offline
            var layerControl = L.control.layers(baseLayers, overlays).addTo(map);
            var makassar = L.marker([-2.644568843057757, 119.61914062500001], {
                icon: logo
            }).bindPopup('This is Crown Hill Park.');
            var ambon = L.marker([-3.4169189298481557, 128.2675775885582], {
                icon: logo
            }).bindPopup('This is Ruby Hill Park.');

            var Offline = L.layerGroup([makassar, ambon]);

            var satellite = L.tileLayer(mbUrl, {
                id: 'mapbox/satellite-v9',
                tileSize: 512,
                zoomOffset: -1,
                attribution: mbAttr
            });
            layerControl.addBaseLayer(satellite, 'Satellite');
            layerControl.addOverlay(Offline, 'Offline');
        });
    </script>
@endsection
