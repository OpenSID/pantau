@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor LayananDesa')

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
                                        <p class="m-0 text-white"><marquee>Info Rilis Terbaru: Rilis LayananDesa 2407.0.0</marquee></p>
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
                        @include('website.layanandesa.peta')
                    </div>
                    <div class="col-lg-2 box-provinsi">
                        <p>Desa / Kelurahan Aktif</p>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-center">
                                    <h3 class="text-blue">3065</h3>
                                    <p class="text-black">Total Desa: 21.304</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="small-box bg-white">
                                <div class="inner text-left">
                                    @include('website.layanandesa.install_baru')
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
                            <div class="text-bold" style="margin-top:-10px">&nbsp;<br>Jumlah Versi LayananDesa</div>
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
        </div>
    </div>
    <div class="card-body">
        <div class="row mt-3">
            <div class="col-lg-8">
                <b>
                    Daftar Pengguna LayananDesa 7 Hari Terakhir
                </b>
                @include('website.layanandesa.tabel')
            </div>
            <div class="col-lg-4">
                <b>
                    Daftar Versi Aplikasi LayananDesa
                </b>
                @include('website.layanandesa.versi')
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


                let _listElm;
                for (let i in detail) {
                    _listElm = $(`#${i}-baru`).find('ol')
                    _listElm.empty()
                    for (let j in detail[i]) {
                        _listElm.append(`<li>${detail[i][j]}</li>`)
                    }
                }


                $.ajax({
                    url: "{{ url('api/web/chart-usage/opensid') }}",
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
