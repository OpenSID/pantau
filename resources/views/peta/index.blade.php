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
    <script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
    <script>
        $(document).ready(function() {
            var url = "{{ url('peta/desa') }}";

            $.ajax({
                type: "GET",
                url: url,
                data: "check",
                success: function(response) {
                    // // Buat peta
                    return peta(response);
                }
            });
        });

        function peta(DaftarDesa) {
            var mapCenter = [
                {{ config('leaflet.map_center_latitude') }},
                {{ config('leaflet.map_center_longitude') }}
            ];
            var mapZoom = {{ config('leaflet.zoom_level') }}

            var mbAttr =
                'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
            var mbUrl =
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

            var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                maxZoom: 18,
                attribution: mbAttr,
            });

            var map = L.map('map', {
                center: mapCenter,
                zoom: mapZoom,
                layers: [tiles]
            });

            var markers = L.markerClusterGroup();

            for (var x = 0; x < DaftarDesa.length; x++) {
                var marker = L.marker(DaftarDesa[x].koordinat, {
                    icon: icon(DaftarDesa[x].logo)
                });
                marker.bindPopup(
                    '<h6><b>' + DaftarDesa[x].sebutan_desa + ' ' + DaftarDesa[x].desa + '</b></h6>' +
                    '<b><table width="100%">' +
                    '<tr>' +
                    '<td>Kecamatan</td><td> : ' + DaftarDesa[x].kecamatan + '</b></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Kab/Kota</td><td> : ' + DaftarDesa[x].kabupaten + '</b></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Provinsi</td><td> : ' + DaftarDesa[x].provinsi + '</b></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Alamat</td><td> : ' + DaftarDesa[x].alamat + '</b></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Website</td><td> : ' + '<a href="' + DaftarDesa[x].web +
                    '" + " target=\"_blank\">' + DaftarDesa[x].web + '</a>' + '</b></td>' +
                    '</tr>' +
                    '</table></b>'
                );
                markers.addLayer(marker);
            }

            map.addLayer(markers);
        }

        function icon(url) {
            var icon = "{{ url('assets/img/opensid_logo.png') }}";

            return L.icon({
                iconUrl: url ?? icon,
                iconSize: [20, 20],
            });
        }
    </script>
@endsection
