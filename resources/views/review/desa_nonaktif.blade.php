@extends('layouts.index')

@section('title', 'Desa Tidak Aktif')

@section('content_header')
    <h1>Desa Tidak Aktif<small class="font-weight-light ml-1 text-md">(Sejak tujuh hari terakhir)</small></h1>
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
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>
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
        const params = new URLSearchParams(window.location.search);
        const $akses = $('#akses');

        if (params.has('akses')) {
            $akses.val(params.get('akses')).change();
            $('#collapse-filter').collapse('show');
        }

        var desa = $('#table-desa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('review/non-aktif') }}`,
                method: 'get',
                data: function(data) {
                    data.akses = $('#akses').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
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
                    orderable: false,
                    defaultContent: '-'
                },
            ]
        });

        $('#filter').on('click', function(e) {
            desa.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#akses').val('0').change();
            desa.ajax.reload();
        });
    </script>
@endsection
