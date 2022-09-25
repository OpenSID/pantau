@extends('layouts.index')

@section('title', 'Profil Versi OpenSID')

@section('content_header')
    <h1>Profil Versi OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Versi yang terpasang di desa OpenSID) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                                aria-expanded="false" aria-controls="collapse-filter">
                                <i class="fas fa-filter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.components.form_filter')
                        </div>
                    </div>
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
                data: function(data) {
                    data.aktif = $('#aktif').val();
                }
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

        $('#filter').on('click', function(e) {
            desa.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#aktif').val('').change();

            desa.ajax.reload();
        });
    </script>
@endsection
