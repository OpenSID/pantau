@extends('layouts.index')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Desa OpenSID<small class="font-weight-light ml-1 text-md">(Desa yang memasang OpenSID)</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @auth
                                        <th>Aksi</th>
                                    @endauth
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Versi Offline</th>
                                    <th>Versi Online</th>
                                    <th>Akses Terakhir</th>
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
        var desa = $('#table-desa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/desa') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                @auth
                    {
                        searchable: false,
                        orderable: false,
                        data: null,
                        render: function (data) {
                            return `<td class="text-right py-0 align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <button data-href="{{ url('laporan/desa') }}/${data.id}" class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>`
                        },
                    },
                @endauth {
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

                {
                    data: 'versi_lokal'
                },

                {
                    data: 'versi_hosting'
                },

                {
                    data: 'tgl_akses',
                    searchable: false,
                },
            ]
        })
    </script>
@endsection
