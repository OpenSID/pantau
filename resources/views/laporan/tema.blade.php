@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa Pengguna Tema Bawaan')

@section('content_header')
    <h1>
        Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan Tema Bawaan
        </small></h1>
@stop

@section('content')    
    @include('layouts.components.notification')
    <div class="row">
        @if(empty(request()->query('tema')))
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{$natra}}</h3>
                                    <p>Tema Natra</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <a href="{{ url('laporan/tema') }}?tema=Natra" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{$esensi}}</h3>
                                    <p>Tema Esensi</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ url('laporan/tema') }}?tema=Esensi" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{$palanta}}</h3>
                                    <p>Tema Palanta</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <a href="{{ url('laporan/tema') }}?tema=Palanta" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @endif
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Desa Pengguna Tema Bawaan {{request()->query('tema') ? '- '.request()->query('tema') : ''}}</h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title=""
                            class="badge badge-primary">
                            @if(request()->query('tema') == 'Esensi')
                            {{$esensi}}
                            @elseif(request()->query('tema') == 'Natra')
                            {{$natra}}
                            @elseif(request()->query('tema') == 'Palanta')
                            {{$palanta}}
                            @else
                            {{$tema}}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.components.form_filter')
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>                                    
                                    <th>Tanggal Terpantau</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Tema</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const params = new URLSearchParams(window.location.search);

        switch (params.get('status')) {
            case '1':
                $('#status').val('1').change();
                filter_open();
                break;
            case '2':
                $('#status').val('2').change()
                filter_open();
                break;

            case '3':
                $('#status').val('3').change()
                filter_open();
                break;

            default:
                break;
        }

        switch (params.get('akses')) {
            case '4':
                $('#akses').val('4').change();
                filter_open();
                break;
            case '5':
                $('#akses').val('5').change();
                filter_open();
                break;

            default:
                break;
        }

        var desa = $('#table-desa').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,

                ajax: {
                    url: `{{ url('laporan/tema') }}{{request()->query('tema') ? '?tema='.request()->query('tema') : ''}}`,
                    method: 'get',
                    data: function(data) {
                        data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                            'kode_provinsi');
                        data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                            'kode_kabupaten');
                        data.kode_kecamatan = $('#kecamatan').val();
                        data.status = $('#status').val();
                        data.akses = $('#akses').val();
                        data.tte = $('#tte').val();
                        data.versi_lokal = params.get('versi_lokal');
                        data.versi_hosting = params.get('versi_hosting');
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                {
                    data: 'updated_at'
                },
                {
                    data: 'nama_desa'
                },
                {
                    data: 'nama_kecamatan'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi',
                },
                {
                    data: 'url_hosting'
                },                               
                {
                    data: 'tema'
                }, ],
            order: [
                [2, 'desc']
            ],
        });

        $('#filter').on('click', function(e) {
            desa.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#status').val('0').change();
            $('#akses').val('0').change();
            $('#tte').val('empty').change();

            desa.ajax.reload();
        });
    </script>
@endsection
