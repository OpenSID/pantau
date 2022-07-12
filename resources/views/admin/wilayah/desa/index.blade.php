@extends('layouts.index')

@section('title', 'Daftar Desa')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Daftar Desa</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <a href="{{ url('desa/create') }}" class="btn btn-primary btn-sm mb-3">Tambah Data</a>
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
                                    <th>NAMA DESA</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                orderable: true,
                ajax: "{{ url('desa') }}",
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
                        data: 'kode_desa',
                        name: 'kode_desa'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'nama_provinsi'
                    },
                    {
                        data: 'nama_kabupaten',
                        name: 'nama_kabupaten'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'nama_kecamatan'
                    },
                    {
                        data: 'nama_desa',
                        name: 'nama_desa'
                    },
                ],
                order: [
                    [2, 'asc']
                ]
            });
        });
    </script>
@endsection
