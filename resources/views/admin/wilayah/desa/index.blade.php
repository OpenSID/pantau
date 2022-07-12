@extends('layouts.index')

@section('title', 'Daftar Desa')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Daftar Desa</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="5%" nowrap>NO</th>
                                    <th width="10%" nowrap>KODE PROVINSI</th>
                                    <th>NAMA PROVINSI</th>
                                    <th width="10%" nowrap>KODE KABUPATEN</th>
                                    <th>NAMA KABUPATEN</th>
                                    <th width="10%" nowrap>KODE KECAMATAN</th>
                                    <th>NAMA KECAMATAN</th>
                                    <th width="10%" nowrap>KODE DESA</th>
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
                ajax: "{{ route('desa.datatables') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'kode_provinsi',
                        name: 'kode_provinsi'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'nama_provinsi'
                    },
                    {
                        data: 'kode_kabupaten',
                        name: 'kode_kabupaten'
                    },
                    {
                        data: 'nama_kabupaten',
                        name: 'nama_kabupaten'
                    },
                    {
                        data: 'kode_kecamatan',
                        name: 'kode_kecamatan'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'nama_kecamatan'
                    },
                    {
                        data: 'region_code',
                        name: 'region_code'
                    },
                    {
                        data: 'region_name',
                        name: 'region_name'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endsection
