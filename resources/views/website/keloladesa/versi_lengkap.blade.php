@extends('layouts.web')

@section('title', 'Versi KelolaDesa')

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
    <h1>Versi KelolaDesa<small class="font-weight-light ml-1 text-md font-weight-bold">(Versi yang terpasang di desa)</small></h1>
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
                                    <th>Jumlah</th>                                    
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

        var layananVersi = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'desc']],
            ajax: {
                url: `{{ url('web/keloladesa/versi') }}`,
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
                    name: 'versi',
                },                
                {
                    name: 'jumlah',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('web/keloladesa/versi/detail') }}?versi=${data.versi}">${data.jumlah}</a>`
                    },
                    searchable: false,
                },                
            ]
        })

        $('#filter').on('click', function(e) {
            layananVersi.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#aktif').val('').change();

            layananVersi.ajax.reload();
        });
    </script>
@endsection