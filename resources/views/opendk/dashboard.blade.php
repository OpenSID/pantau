@extends('layouts.index')

@section('title', 'Dasbor')

@section('content_header')
    <h1>
        Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan OpenDK @if ($provinsi = session('provinsi'))
                {{ "| {$provinsi->nama_prov}" }}
            @endif
        </small></h1>
@stop

@section('content')
    @include($baseView.'.summary.kecamatan')
    @include($baseView.'.summary.kabupaten')

    <div class="row">
        <div class="col-lg-12">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Kabupaten yang belum ada OpenDK</h3>
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
                    data: 'kode_kabupaten'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'jumlah'
                },
            ]
        })
    </script>
@stop
