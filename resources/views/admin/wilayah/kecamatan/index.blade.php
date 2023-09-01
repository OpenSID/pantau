@extends('layouts.index')

@section('title', 'Daftar Kecamatan')

@section('content_header')
    <h1>Kecamatan<small class="font-weight-light ml-1 text-md">Daftar Kecamatan</small></h1>
@stop

@section('content')
    @include('layouts.components.notification')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <a href="{{ route('kecamatan.create') }}" class="btn btn-success btn-sm mb-3"><i class="fas fa-plus"></i>
                        &ensp;Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="5%" nowrap>NO</th>
                                    <th width="5%" nowrap>AKSI</th>
                                    <th width="10%" nowrap>KODE WILAYAH</th>
                                    <th>NAMA PROVINSI</th>
                                    <th>NAMA KABUPATEN</th>
                                    <th>NAMA KECAMATAN</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.components.global_delete')
@endsection
@section('js')
    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                orderable: true,
                ajax: "{{ url('kecamatan') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'kode_kecamatan',
                        name: 'tbl_regions.region_code'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'prov.region_name'
                    },
                    {
                        data: 'nama_kabupaten',
                        name: 'kab.region_name'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'tbl_regions.region_name',
                    },
                ],
                order: [
                    [2, 'asc']
                ]
            });
        });
    </script>
@endsection
