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
                                <h3 class="card-title">Pengguna Openkab</h3>                
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="table-pengguna-openkab">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal Terpantau</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
                                                <th>Nama Aplikasi</th>
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
        $('#table-pengguna-openkab').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:pengguna-openkab') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'nama_kab'
                },
                {
                    data: 'nama_prov'
                },
                {
                    data: 'nama_aplikasi'
                },
            ]
        })
    })
</script>
@endpush

