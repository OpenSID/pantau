@extends('layouts.index')

@section('title', 'Daftar Kabupaten')

@section('content_header')
    <h1>Kabupaten<small class="font-weight-light ml-1 text-md">Daftar Kabupaten</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('kabupaten/datatables') }}"><i class="fas fa-file-excel"></i> Excels<a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="5%" nowrap>NO</th>
                                    <th width="10%" nowrap>KODE WILAYAH</th>
                                    <th>NAMA PROVINSI</th>
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
                        data: 'kode_kabupaten',
                        name: 'tbl_regions.region_code'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'prov.region_name'
                    },
                    {
                        data: 'nama_kabupaten',
                        name: 'tbl_regions.region_name'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
        $('#btn-export').click(function(){
            const _href = $(this).data('href')
            window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#datatable').DataTable().ajax.params())
        })
    </script>
@endsection
