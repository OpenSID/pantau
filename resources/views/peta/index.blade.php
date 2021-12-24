@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa Pengguna OpenSID')

@section('content_header')
    <h1>Desa Pengguna OpenSID<small class="font-weight-light ml-1 text-md">Pengguna aktif dalam 7 hari terakhir</small></h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
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
                        <label>Desa</label>
                        <select class="select2 form-control-sm" id="desa" name="desa" data-placeholder="Pilih Desa"
                            style="width: 100%;">
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="map" style="width: 1800px; height: 400px;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            var map = L.map('map').setView([51.505, -0.09], 13);

            var tiles = L.tileLayer(
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={{ config('tracksid.sandi.mapbox_token') }}', {
                    maxZoom: 18,
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1
                }).addTo(map);
        });
    </script>
@endsection
