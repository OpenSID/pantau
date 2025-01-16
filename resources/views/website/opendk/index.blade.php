@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor OpenDK')

@section('content_header')

@stop

@section('content')
@include('layouts.components.daterangepicker')
<div class="row">
    <div class="col-12">
        <div class="card bg-gray-light">
            <!-- /.card-header -->
            <div class="card-header header-bg">
                <div class="row p-1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-10 align-content-center">
                                <div class="d-flex">
                                    <a class="btn btn-sm btn-secondary align-content-center" data-toggle="collapse" href="#collapse-filter" role="button"
                                        aria-expanded="false" aria-controls="collapse-filter">
                                        <i class="fas fa-filter"></i>
                                    </a>
                                    <div class="bg-blue p-1 ml-1" style="width: 100%">
                                        <p class="m-0 text-white"><marquee>Info Rilis Terbaru: {{ $info_rilis }}</marquee></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" name="periods" class="form-control datepicker"
                                        data-option='{!! json_encode(array_merge(config("local.daterangepicker"), config("local.daterangepicker_range"), ["autoApply" => false, "singleDatePicker" =>false])) !!}'
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form name="filter" method="GET">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('layouts.components.form_filter')
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-10">
                        @include('website.opendk.peta')
                    </div>
                    <div class="col-lg-2 box-provinsi">
                        <p>Kecamatan Aktif</p>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-center">
                                    <h3 class="text-blue">{{ $pengguna_opendk }}</h3>
                                    <p class="text-black">Total Desa: {{ $total_desa }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-left">
                                    @include('website.opendk.install_baru')
                                </div>
                            </div>
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
                    OpenDK Terpasang Berdasarkan Bulan
                </div>
                @include('website.opendk.chart')
            </div>
            <div class="col-lg-4">
                <div class="bg-blue p-2">
                    OpenDK Terpasang Berdasarkan Provinsi
                </div>
                @include('website.opendk.provinsi_pengguna', ['provinsi_pengguna' =>
                $provinsi_pengguna_opendk])
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-8">
                <b>
                    Daftar Pengguna OpenDK 7 Hari Terakhir
                </b>
                @include('website.opendk.tabel')
            </div>
            <div class="col-lg-4">
                <b>
                    Daftar Versi Aplikasi OpenDK
                </b>
                @include('website.opendk.versi')
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
</div>
</div>
@stop

@push('js')
<script>
    $('.datepicker').each(function () {
        const _options = $(this).data('option')
        $(this).daterangepicker(_options)
    })

    function updateData() {
        const params = {
            period: $('input[name=periods]').val(),
            provinsi: $('select[name=provinsi]').val(),
            kabupaten: $('select[name=kabupaten]').val(),
            kecamatan: $('select[name=kecamatan]').val()
        }        

        $.ajax({
            url: "{{ url('api/web/chart-opendk') }}",
            data: params,
            type: "GET",
            success: function (data) {
                myChart.data = data;
                myChart.update();
            }
        }, 'json')
    }

    $(document).ready(function () {

        // set default kosongkan datepicker
        $('input[name=periods]').val('');

        $('#filter').click(function () {
            updateData()
        })
        $('input[name=periods]').change(function () {
            updateData()
        })
        $('#reset').click(function () {
            $('#collapse-filter select').val('')
        })

        updateData()
    })
</script>
@endpush
