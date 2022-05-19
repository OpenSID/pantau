@extends('layouts.index')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard<small class="font-weight-light ml-1 text-md">Status Penggunaan OpenSID</small></h1>
@stop

@section('content')
    <div class="card card-outline card-info col-lg-12">
        <div class="card-header">
            <h3 class="card-title">Desa Pengguna</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->aktif }}</h3>
                            <p>Desa Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->desa_online }}</h3>
                            <p>Desa Online</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->desa_offline }}</h3>
                            <p>Desa Offline</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total Desa</span>
                            <span class="info-box-number">{{ $jumlahDesa->desa_total }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Tidak aktif 4 bulan</span>
                            <span class="info-box-number">{{ $jumlahDesa->tidak_aktif }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-lg-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Bukan desa terdaftar</span>
                            <span class="info-box-number">{{ $jumlahDesa->bukan_desa }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Desa baru dalam 7 hari terakhir</h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="{{ $desaBaru }} Desa Baru" class="badge badge-primary">{{ $desaBaru }}</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-desa-baru">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div class="card col-lg-12">
        <div class="card-header">
            <h3 class="card-title">Kabupaten Pengguna</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->kabupaten_total }}</h3>
                            <p>Total Kabupaten</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->kabupaten_online }}</h3>
                            <p>Kabupaten Online</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $jumlahDesa->kabupaten_offline }}</h3>
                            <p>Kabupaten Offline</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Kabupaten yang belum ada desa OpenSID</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-kabupaten-kosong">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Kabupaten</th>
                                    <th>Nama Kabupaten</th>
                                    <th>Nama Provinsi</th>
                                    <th>Jumlah Desa</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var desaBaru = $('#table-desa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:desa-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
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
                    data: 'nama_provinsi'
                },
                {
                    data: 'url_hosting'
                },
            ]
        })

        var kabupatenKosong = $('#table-kabupaten-kosong').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:kabupaten-kosong') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'region_code'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'jml_desa'
                },
            ]
        })
    </script>
@stop
