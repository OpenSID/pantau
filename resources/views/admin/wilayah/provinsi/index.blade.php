@extends('layouts.index')

@section('title', 'Daftar Provinsi')

@section('content_header')
<h1>Provinsi<small class="font-weight-light ml-1 text-md">Daftar Provinsi</small></h1>
@stop

@section('content')
@include('layouts.components.notification')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline card-primary">
            <div class="card-header with-border">
                <a href="{{ route('provinsi.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>
                    &ensp;Tambah</a>
                <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('provinsi') }}"><i
                        class="fas fa-file-excel"></i> Excel<a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatable">
                        <thead>
                            <tr>
                                <th width="5%" nowrap>NO</th>
                                <th width="5%" nowrap>AKSI</th>
                                <th width="20%" nowrap>KODE WILAYAH</th>
                                <th>PROVINSI</th>
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
                ajax: "{{ url('provinsi') }}",
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
                        data: 'kode_provinsi',
                        name: 'tbl_regions.region_code'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'tbl_regions.region_name'
                    },
                ],
                order: [
                    [2, 'asc']
                ]
            });
        });
        
        $('#btn-export').click(function(){
            const _href = $(this).data('href')
            window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#datatable').DataTable().ajax.params())
        })
</script>
@endsection