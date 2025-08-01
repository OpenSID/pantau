@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor')

@section('content_header')

@stop

@section('content')
    @include('layouts.components.daterangepicker')
    <div class="col-12">
        <div class=" bg-gray-light">
            <div class="card-body">
                <div class="row">
                    <h1>Pengguna Aplikasi OpenSID</small></h1>
                    <div class="col-lg-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter"
                                            role="button" aria-expanded="false" aria-controls="collapse-filter">
                                            <i class="fas fa-filter"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('layouts.components.form_filter')
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table" id="table-pengguna-opensid">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Desa</th>
                                                <th>Kecamatan</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
                                                <th>Versi Offline</th>
                                                <th>Versi Online</th>
                                                <th>Modul TTE</th>
                                                <th>Surat ter-TTE</th>
                                                <th>Akses Terakhir</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
@stop


@push('js')
    <script>
        $(document).ready(function() {
            const semuaDesa = $('#table-pengguna-opensid').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                ajax: {
                    url: `{{ route('datatables:pengguna-opensid') }}`,
                    data: function(d) {
                        d.kode_provinsi = $('#provinsi').val();
                        d.kode_kabupaten = $('#kabupaten').val();
                        d.kode_kecamatan = $('#kecamatan').val();
                        d.status = $('#status').val();
                        d.akses = $('#akses').val();
                        d.tte = $('#tte').val();
                    },
                    method: 'get',
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
                        data: 'versi_lokal'
                    },
                    {
                        data: 'versi_hosting'
                    },
                    {
                        data: 'modul_tte',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'jml_surat_tte'
                    },
                    {
                        data: 'tanggal',
                        searchable: false,
                        orderable: false
                    },
                ]
            })

            $('#filter').click(function() {
                semuaDesa.ajax.reload();
            })
            $('#reset').click(function() {
                $('#kode_provinsi').val('').trigger('change');
                $('#kode_kabupaten').val('').trigger('change');
                $('#kode_kecamatan').val('').trigger('change');
                $('#status').val('').trigger('change');
                $('#akses').val('').trigger('change');
                $('#tte').val('').trigger('change');
                semuaDesa.ajax.reload();
            })
        })
    </script>
@endpush
