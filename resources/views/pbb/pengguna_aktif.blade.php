@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'PBB Pengguna Aktif')

@section('content_header')
<h1>
    PBB Pengguna Aktif<small
        class="font-weight-light ml-1 text-md font-weight-bold">@if ($provinsi = session('provinsi'))
            | {{ $provinsi->nama_prov ?? $provinsi }}
        @endif
    </small></h1>
@stop

@section('content')
    @include('layouts.components.notification')
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
                        <table class="table" id="table-pbb-pengguna-aktif">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Versi</th>
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

        var pbbPenggunaAktif = $('#table-pbb-pengguna-aktif').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,

            ajax: {
                url: `{{ url('pbb/pengguna-aktif') }}`,
                method: 'get',
                data: function (data) {
                    data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                        'kode_provinsi');
                    data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                        'kode_kabupaten');
                    data.kode_kecamatan = $('#kecamatan').val();
                    // filter pbb might use different param names if needed, 
                    // but following opendk pattern for now
                    data.akses_opendk = $('#akses_opendk').val();
                    data.versi_opendk = $('#versi_opendk').val();
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
                data: 'versi'
            },
            {
                data: 'akses_terakhir',
                orderable: true
            }],
            order: [
                [6, 'desc']
            ],
        });

        $('#filter').on('click', function (e) {
            pbbPenggunaAktif.draw();
        });

        $(document).on('click', '#reset', function (e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#akses_opendk').val('').change();
            $('#versi_opendk').val('').change();

            pbbPenggunaAktif.ajax.reload();
        });
    </script>
@endsection