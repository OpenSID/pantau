@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor')

@section('content_header')

@stop

@section('content')
@include('layouts.components.daterangepicker')
<div class="row">
    <div class="col-12">
        <div class=" bg-gray-light">
            <div class="card-body">
                <div class="row">
                    <h1>Pengguna Aplikasi Layanan Desa</h1>
                    <div class="col-lg-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-sm btn-secondary" data-toggle="collapse"
                                            href="#collapse-filter" role="button" aria-expanded="false"
                                            aria-controls="collapse-filter">
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
                                    <table class="table" id="table-pengguna-layanandesa">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Desa</th>
                                                <th>Kecamatan</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
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
</div>
@stop


@push('js')
<script>
    $(document).ready(function() {
        $('#table-pengguna-layanandesa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:pengguna-layanandesa') }}`,
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
                    data: 'nama_desa'
                },
                {
                    data: 'nama_kec'
                },
                {
                    data: 'nama_kab'
                },
                {
                    data: 'nama_prov'
                },
                {
                    data: 'tgl_akses'
                },
            ]
        })

        $('#filter').on('click', function(e) {
            // e.preventDefault();
            $('#table-pengguna-layanandesa').DataTable().draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#table-pengguna-layanandesa').DataTable().draw();
        });
    })
</script>
@endpush