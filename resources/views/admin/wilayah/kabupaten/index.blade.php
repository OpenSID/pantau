@extends('layouts.index')

@section('title', 'Daftar Kabupaten')

@section('content_header')
    <h1>Kabupaten<small class="font-weight-light ml-1 text-md">Daftar Kabupaten</small></h1>
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
                ajax: "{{ route('kabupaten.datatables') }}",
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
