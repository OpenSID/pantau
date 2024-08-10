@extends('layouts.web')

@section('title', 'Versi OpenSID')

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
    <h1>Versi OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Versi yang terpasang di desa OpenSID)</small></h1>
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
                                    <th>Online</th>
                                    <th>Offline</th>                                    
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
        $('#aktif').select2();

        var desa = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'desc']],
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
                    name: 'x.versi',
                },                
                {
                    name: 'online',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('web/opensid/versi/detail') }}?versi_hosting=${data.versi}">${data.online}</a>`
                    },
                    searchable: false,
                },
                {
                    name: 'offline',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('web/opensid/versi/detail') }}?versi_lokal=${data.versi}">${data.offline}</a>`
                    },
                    searchable: false,
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
