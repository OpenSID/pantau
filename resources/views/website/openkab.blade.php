@extends('layouts.web')
@section('title', 'Dasbor OpenKab')

@section('content_header')

@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card bg-gray-light">
            <!-- /.card-header -->
            <div class="card-header header-bg">
                <div class="row p-1 mt-3 bg-blue">
                    <div class="col-sm-12">
                        <p class="m-0 text-white">Info Rilis Terbaru: Rilis OpenKab {{ $latestVersion->versi }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-10">
                        @include('website.partial.peta_openkab')
                    </div>
                    <div class="col-lg-2 box-provinsi">
                        <div class="col-xs-12">
                            <div class="small-box bg-green">
                                <div class="inner text-center">
                                    <h3 class="text-white">{{ $jumlahProvinsi }}</h3>
                                    <p class="text-white">Provinsi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="small-box bg-blue">
                                <div class="inner text-center">
                                    <h3 class="text-white">21</h3>
                                    <p class="text-white">Terpasang <br> Versi Terakhir: 2407.0.0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-lg-8">
                        <div class="bg-blue p-2">
                            Pengguna OpenKab
                        </div>
                        @include('website.partial.tabel_openkab')
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-blue p-2">
                            Pengguna OpenSID Terpasang Seluruh Kabupaten
                        </div>
                        @include('website.partial.provinsi_opensid')
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@stop