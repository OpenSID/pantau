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
                                    @if(request()->has('nama_kabupaten'))
                                        <h4>Kabupaten {{ request('nama_kabupaten') }}</h3>
                                    @endif
                                    <div class="d-flex">
                                        <a class="btn btn-sm btn-secondary align-content-center" data-toggle="collapse"
                                            href="#collapse-filter" role="button" aria-expanded="false"
                                            aria-controls="collapse-filter">
                                            <i class="fas fa-filter"></i>
                                        </a>
                                        <div class="bg-blue p-1 ml-1" style="width: 100%">
                                            <p class="m-0 text-white">
                                                <marquee>Info Rilis Terbaru: Rilis Umum {{ $latestUmumVersion }} | Rilis
                                                    Premium {{ $latestPremiumVersion }}</marquee>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="text" name="periods" class="form-control datepicker"
                                            value="{{ implode(' - ', config('local.daterangepicker_range.ranges')['30 Hari Terakhir']) }}"
                                            data-option='{!! json_encode(
                                                array_merge(config('local.daterangepicker'), config('local.daterangepicker_range'), [
                                                    'autoApply' => false,
                                                    'singleDatePicker' => false,
                                                ]),
                                            ) !!}' autocomplete="off">
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
                                <div class="small-box bg-white block_desa_aktif">
                                    <div class="inner text-center">
                                        <h3 class="text-blue" id="desa_aktif">0</h3>
                                        <p class="text-black">Total Desa: <span id="total_desa"></span></p>
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
                    <div class="row" id="block_pengguna_selain_opensid">
                        <div class="col-xl-6">
                            <div class="small-box bg-blue rounded-lg">
                                <div class="row p-3">
                                    <div class="col-md-2 text-center align-content-center">
                                        <i class="fas fa-building fa-6x text-white"></i>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Aplikasi PBB</h4>
                                        <div class="display-4 text-bold mt-n3" id="pengguna_pbb">0</div>
                                        <div class="text-bold mt-n2">Pengguna Terpasang</div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <h3><span class="badge badge-warning rounded-pill">versi {{ $versi_pbb }}</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="small-box bg-blue rounded-lg">
                                <div class="row p-3">
                                    <div class="col-md-2 text-center align-content-center">
                                        <i class="fas fa-building fa-6x text-white"></i>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Anjungan Mandiri</h4>
                                        <div class="display-4 text-bold mt-n3" id="pengguna_anjungan">0</div>
                                        <div class="text-bold mt-n2">Pengguna Terpasang</div>
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
                            @include('website.partial.provinsi_pengguna_opensid', [
                                'provinsi_pengguna_opensid' => $provinsi_pengguna_opensid,
                            ])
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <b class="mb-0">
                                    Daftar Pengguna OpenSID 7 Hari Terakhir
                                </b>
                                <a href="{{ url('web/opensid-data') }}" class="btn btn-sm btn-primary">Data
                                    Selengkapnya</a>
                            </div>

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

    @include('website.partial.modal_pengguna_opensid')
@stop

@push('js')
    <script>

            const filters = {!! json_encode(request()->all()) !!};

            // Tunggu select2 selesai diinisialisasi
            setTimeout(function() {
                if (filters.kode_provinsi) {
                    let option = new Option(filters.nama_provinsi, filters.kode_provinsi, true, true);
                    $('#provinsi').append(option).trigger('change');

                    // Set kabupaten setelah delay
                    if (filters.kode_kabupaten) {
                        let optionKab = new Option(filters.nama_kabupaten, filters.kode_kabupaten, true, true);
                        $('#kabupaten').attr('disabled', false);
                        $('#kabupaten').append(optionKab).trigger('change');
                    }

                    $('#filter').trigger('click');
                }
            }, 1000);

            $('.datepicker').each(function() {
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
                beforeSend: function() {
                    $('#box-provinsi>.total').text('..')
                    $('#box-kabupaten>.total').text('..')
                    $('#box-kecamatan>.total').text('..')
                    $('#box-desa>.total').text('..')
                },
                success: function(data) {
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
                        data: {
                            period: $('input[name=periods]').val(),
                            kode_provinsi: $('select[name=provinsi]').val(),
                            kode_kabupaten: $('select[name=kabupaten]').val(),
                            kode_kecamatan: $('select[name=kecamatan]').val(),
                        },
                        type: "GET",
                        success: function(data) {
                            myChart.data = data;
                            myChart.update();
                        }
                    }, 'json')
                }
            }, 'json')

            $('.block_desa_aktif').trigger('change')
            $('#block_install_baru').trigger('change')
            $('#block_pengguna_selain_opensid').trigger('change')
            $('#block_table_desa_baru').trigger('change')
            $('#block_table_versi').trigger('change')
        }

        $(document).ready(function() {
            $('#filter').click(function() {
                updateData()
            })
            $('input[name=periods]').change(function() {
                updateData()
            })
            $('#reset').click(function() {
                $('#collapse-filter select').val('')
            })

            $('.block_desa_aktif').change(function() {
                const params = {
                    kode_provinsi: $('select[name=provinsi]').val(),
                    kode_kabupaten: $('select[name=kabupaten]').val(),
                    kode_kecamatan: $('select[name=kecamatan]').val(),
                }
                $.get("{{ url('api/web/desa-aktif-opensid') }}", params, function(data) {
                    $('#desa_aktif').text(data.aktif)
                    $('#total_desa').text(data.desa_total)
                }, 'json')
            })

            $('#block_pengguna_selain_opensid').change(function() {
                const params = {
                    kode_provinsi: $('select[name=provinsi]').val(),
                    kode_kabupaten: $('select[name=kabupaten]').val(),
                    kode_kecamatan: $('select[name=kecamatan]').val(),
                }

                $.get("{{ url('api/web/pengguna-selain-opensid') }}", params, function(data) {
                    $('#block_pengguna_selain_opensid').find('#pengguna_pbb').text(data.pbb)
                    $('#block_pengguna_selain_opensid').find('#pengguna_anjungan').text(data.anjungan)
                }, 'json')
            })
        })
    </script>
@endpush
