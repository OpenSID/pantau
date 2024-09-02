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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Pengguna Aplikasi PBB</h3>                
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="table-pengguna-pbb">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Desa</th>
                                                <th>Kecamatan</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
                                                <th>URL</th>
                                                <th>Versi</th>
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
        $('#table-pengguna-pbb').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:pengguna-pbb') }}`,
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
                    data: 'url'
                },
                {
                    data: 'versi'
                },
                {
                    data: 'tanggal'
                },
            ]
        })
    })
</script>
@endpush
