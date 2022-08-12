@extends('layouts.index')

@section('title', 'Kabupaten OpenSID')

@section('content_header')
    <h1>Kabupaten OpenSID<small class="font-weight-light ml-1 text-md">(Kabupaten yang memasang OpenSID)</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-kabupaten">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Server Offline</th>
                                    <th>Server Online</th>
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
        var desa = $('#table-kabupaten').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/kabupaten') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'nama_kabupaten',
                    name: 'sub.nama_kabupaten',
                },
                {
                    data: 'nama_provinsi',
                    name: 'sub.nama_provinsi',
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=2&kode_kabupaten=${data.kode_kabupaten}&kode_provinsi=${data.kode_provinsi}">${data.offline}</a>`
                    },
                    searchable: false,
                    name: 'offline',
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=1&kode_kabupaten=${data.kode_kabupaten}&kode_provinsi=${data.kode_provinsi}">${data.online}</a>`
                    },
                    searchable: false,
                    name: 'online',
                },
            ]
        })
    </script>
@endsection
