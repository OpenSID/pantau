@extends('layouts.index')

@section('title', 'Dasbor OpenKab')

@section('content_header')
<h1>
    Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status OpenKab
    </small></h1>
@stop

@section('content')    
    @include('layouts.components.notification')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $jumlahProvinsi }}</h3>
                                    <p>Provinsi Terpasang</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="small-box-footer" style="height: 40px;"></div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totalKabupaten }}</h3>
                                    <p>Total Kabupaten</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-city"></i>
                                </div>
                                <div class="small-box-footer" style="height: 40px;"></div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4">
                            <!-- small card -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $kabupatenTerpasang }}</h3>
                                    <p>Kabupaten Terpasang</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-server"></i>
                                </div>
                                <div class="small-box-footer" style="height: 40px;"></div>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar OpenKab</h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="" class="badge badge-primary">
                            {{ $totalKabupaten }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-openkab">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tgl Terpantau</th>
                                    <th>Aplikasi</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Versi</th>
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
        var openkab = $('#table-openkab').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/openkab') }}`,
                method: 'get'
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'tgl_rekam'
            },
            {
                data: 'nama_aplikasi',
                name: 'nama_aplikasi'
            },
            {
                data: 'nama_wilayah',
                name: 'nama_kab'
            },
            {
                data: 'nama_prov'
            },
            {
                data: 'url'
            },
            {
                data: 'versi'
            },
            ],
            order: [
                [1, 'desc']
            ],
        });
    </script>
@endsection