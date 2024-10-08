@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Versi OpenDK')

@section('content')    
    <div class="row">
    <h1>Versi OpenDK<small class="font-weight-light ml-1 text-md font-weight-bold">(Versi yang terpasang di kecamatan)</small></h1>
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

        var opendkVersi = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'desc']],
            ajax: {
                url: `{{ url('web/opendk/versi') }}`,
                method: 'get',
                data: function(data) {
                    data.kode_provinsi = $('#provinsi').val();
                    data.kode_kabupaten = $('#kabupaten').val();
                    data.kode_kecamatan = $('#kecamatan').val();
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
                        return `<a target="_blank" href="{{ url('web/opendk/versi/detail') }}?versi=${data.versi}">${data.jumlah}</a>`
                    },
                    searchable: false,
                },                
            ]
        })

        $('#filter').on('click', function(e) {
            opendkVersi.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            opendkVersi.draw();
        });
    </script>
@endsection