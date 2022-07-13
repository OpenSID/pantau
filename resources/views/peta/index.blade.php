@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <style>
        #map {
            width: 100%;
            height: 63vh;
        }
    </style>
@endpush
@section('title', 'Desa Pengguna OpenSID')

@section('content_header')
    <h1>Desa Pengguna OpenSID<small class="font-weight-light ml-1 text-md">Pengguna aktif dalam 7 hari terakhir</small></h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            <div id="map"></div>
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
            var token = "{{ config('tracksid.sandi.mapbox_token') }}";
            var mapCenter = [
                {{ config('leaflet.map_center_latitude') }},
                {{ config('leaflet.map_center_longitude') }}
            ];

            // {{ request('latitude', config('leaflet.map_center_latitude')) }},
            // {{ request('longitude', config('leaflet.map_center_longitude')) }}

            var map = L.map('map').setView(mapCenter, {{ config('leaflet.zoom_level') }});

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
        });
    </script>
@endsection
