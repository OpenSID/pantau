@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor OpenSID')

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
                                        <p class="m-0 text-white"><marquee>Info Rilis Terbaru: Rilis Umum {{ $latestUmumVersion }} | Rilis Premium {{ $latestPremiumVersion }}</marquee></p>
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
                        @include('website.partial.peta_opensid')
                    </div>
                    <div class="col-lg-2 box-provinsi">
                        <p>Desa / Kelurahan Aktif</p>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-center">
                                    <h3 class="text-blue">{{ $statistikDesa->aktif }}</h3>
                                    <p class="text-black">Total Desa: {{ $statistikDesa->desa_total }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-left">
                                    @include('website.partial.desa_opensid')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0 text-center">
                    <div class="col-8">
                        <div class="p-2 bg-white rounded-lg">
                            @include('website.partial.summary', ['barisTambahan' => true])
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="p-2 bg-green rounded-lg">
                            <div class="display-4 text-bold total">{{ $total_versi }}</div>
                            <div class="text-bold" style="margin-top:-10px">&nbsp;<br>Jumlah Versi OpenSID</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="p-2 bg-blue rounded-lg" id="box-install_versi_terakhir">
                            <div class="display-4 text-bold total">0</div>
                            <div class="text-bold" style="margin-top:-10px">Terpasang <br>Versi Terakhir
                                {{ $versi_terakhir }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="small-box bg-blue rounded-lg">
                            <div class="row p-2">
                                <div class="col-md-2 text-center align-content-center">
                                    <i class="fas fa-building fa-3x text-white"></i>
                                </div>
                                <div class="col-md-8">
                                    <h4>Aplikasi PBB</h4>
                                    <p class="m-0">2213</p>
                                    <p class="m-0">Pengguna Terpasang</p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge badge-warning">versi 24.07.0.0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="small-box bg-blue rounded-lg">
                            <div class="row p-2">
                                <div class="col-md-2 text-center align-content-center">
                                    <i class="fas fa-building fa-3x text-white"></i>
                                </div>
                                <div class="col-md-8">
                                    <h4>Anjungan Mandiri</h4>
                                    <p class="m-0">1823</p>
                                    <p class="m-0">Pengguna Terpasang</p>
                                </div>
                                <div class="col-md-2 text-center">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-8">
                        <div class="bg-blue p-2">
                            OpenSID Terpasang Berdasarkan Bulan
                        </div>
                        @include('website.partial.chart_opensid')
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-blue p-2">
                            OpenSID Terpasang Berdasarkan Provinsi
                        </div>
                        @include('website.partial.provinsi_pengguna_opensid', ['provinsi_pengguna_opensid' => $provinsi_pengguna_opensid])
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-8">
                        <b>
                            Daftar Pengguna OpenSID 7 Hari Terakhir
                        </b>
                        @include('website.partial.tabel_opensid')
                    </div>
                    <div class="col-lg-4">
                        <b>
                            Versi Yang Terpasang Di Desa OpenSID
                        </b>
                        @include('website.partial.versi_opensid')
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
            kecamatan: $('select[name=kecamatan]').val(),
            versi_opensid: '{{ $versi_terakhir }}'
        }

        $.ajax({
            url: "{{ url('api/web/summary') }}",
            data: params,
            type: "GET",
            beforeSend: function () {
                $('#box-provinsi>.total').text('..')
                $('#box-kabupaten>.total').text('..')
                $('#box-kecamatan>.total').text('..')
                $('#box-desa>.total').text('..')
            },
            success: function (data) {
                const total = data.total
                const detail = data.detail
                const additional = data.additional
                $('#box-provinsi>.total').text(total.provinsi.total)
                $('#box-provinsi span.pertumbuhan').html(`<a href="#" class="${total.provinsi.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.provinsi.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.provinsi.pertumbuhan}</span></a>`)
                $('#box-kabupaten>.total').text(total.kabupaten.total)
                $('#box-kabupaten span.pertumbuhan').html(`<a href="#" class="${total.kabupaten.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.kabupaten.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.kabupaten.pertumbuhan}</span></a>`)
                $('#box-kecamatan>.total').text(total.kecamatan.total)
                $('#box-kecamatan span.pertumbuhan').html(`<a href="#" class="${total.kecamatan.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.kecamatan.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.kecamatan.pertumbuhan}</span></a>`)
                $('#box-desa>.total').text(total.desa.total)
                $('#box-desa span.pertumbuhan').html(`<a href="#" class="${total.desa.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.desa.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.desa.pertumbuhan}</span></a>`)
                $('#box-install_versi_terakhir>.total').text(additional.opensid.install_versi_terakhir)

                let _listElm;
                for (let i in detail) {
                    _listElm = $(`#${i}-baru`).find('ol')
                    _listElm.empty()
                    for (let j in detail[i]) {
                        _listElm.append(`<li>${detail[i][j]}</li>`)
                    }
                }


                $.ajax({
                    url: "{{ url('api/web/chart-opensid') }}",
                    data: params,
                    type: "GET",
                    success: function (data) {
                        myChart.data = data;
                        myChart.update();
                    }
                }, 'json')
            }
        }, 'json')
    }

    $(document).ready(function () {
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
