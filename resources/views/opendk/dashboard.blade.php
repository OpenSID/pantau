@extends('layouts.index')

@section('title', 'Dashboard')

@section('content_header')
    <h1>
        Dashboard<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan OpenDK @if ($provinsi = session('provinsi'))
                {{ "| {$provinsi->nama_prov}" }}
            @endif
        </small></h1>
@stop

@section('content')
    <div class="card card-outline card-info col-lg-12">
        <div class="card-header">
            <h3 class="card-title">Kecamatan Pengguna</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $total_semua }}</h3>
                            <p>Total Kabupaten</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="{{ url('laporan/kabupaten') }}" class="small-box-footer">Lihat detail <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $total_aktif }}</h3>
                            <p>Kabupaten Pengguna Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ url('laporan/kabupaten') }}?status=1" class="small-box-footer">Lihat detail <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4">
                    <!-- small card -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $total_terbaru }}</h3>
                            <p>Kabupaten Pengguna OpenDK Versi Terbaru</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <a href="{{ url('laporan/kabupaten') }}?status=3" class="small-box-footer">Lihat detail <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>

    <div class="card card-outline card-info col-lg-12">
        <div class="card-header">
            <h3 class="card-title">Kecamatan baru dalam 7 hari terakhir</h3>
            <div class="card-tools">
                <span data-toggle="tooltip" title="{{ count($daftar_baru) }} Desa Baru"
                    class="badge badge-primary">{{ count($daftar_baru) }}</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="table-kec-baru">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tgl Terpantau</th>
                            <th>Kecamatan</th>
                            <th>Kabupaten</th>
                            <th>Provinsi</th>
                            <th>Web</th>
                            <th>Versi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftar_baru as $key => $item)
                            <tr>
                                <td>{{ $key +1 }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->nama_kecamatan }}</td>
                                <td>{{ $item->nama_kabupaten }}</td>
                                <td>{{ $item->nama_kabupaten }}</td>
                                <td>{{ $item->url }}</td>
                                <td>{{ $item->versi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.card-body -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Kabupaten yang belum ada Kecamatan OpenDK</h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title=" Kabupaten Belum ada OpenSID"
                            class="badge badge-primary"></span>
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
                                    <th>Jumlah Kecamatan</th>
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

@stop

@section('js')
    <script>
        $('#table-kec-baru').DataTable();

        $('#table-kabupaten-kosong').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('opendk.kabupatenkosong') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'kode_kab'
                },
                {
                    data: 'nama_kab'
                },
                {
                    data: 'nama_prov'
                },
                {
                    data: 'jumlah'
                },
            ]
        })
    </script>
@stop
