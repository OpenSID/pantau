@extends('layouts.index')

@section('title', 'Profil Versi OpenSID')

@section('content_header')
    <h1>Profil Versi OpenSID<small class="font-weight-light ml-1 text-md">(Versi yang terpasang di desa OpenSID)</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-versi">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Versi</th>
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
        var desa = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/versi') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'versi',
                    orderable: false
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?versi_lokal=${data.versi}">${data.offline}</a>`
                    }
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?versi_hosting=${data.versi}">${data.online}</a>`
                    }
                },
            ]
        })
    </script>
@endsection
