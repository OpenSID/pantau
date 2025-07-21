@extends('layouts.index')

@section('title', 'Daftar Pekerjaan PMI')

@section('content_header')
<h1>Daftar Pekerjaan PMI</h1>
@stop

@push('styles')
<style>
    .card .card-outline .card-primary {
        margin-bottom: 5rem !important;
    }
</style>
@endpush

@section('content')
@include('layouts.components.notification')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline card-primary" style="margin-bottom: 5rem !important;">
            <div class="card-header with-border">
                <a href="{{ route('pekerjaan-pmi.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>
                    &ensp;Tambah</a>
                <a href="{{ route('pekerjaan-pmi.import') }}" class="btn btn-primary btn-sm"><i
                        class="fas fa-upload"></i>
                    &ensp;Import</a>
                <a class="btn btn-sm btn-success" id="btn-export" role="button"
                    data-href="{{ url('pekerjaan-pmi') }}"><i class="fas fa-file-excel"></i> Excel</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Aksi</th>
                                <th>Nama Pekerjaan PMI</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.components.global_delete')
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('/vendor/datatables/datatables.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('/vendor/datatables/datatables.min.js') }}"></script>
<script>
    $(function() {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: "{{ url('pekerjaan-pmi') }}",
                type: 'GET',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama',
                    name: 'nama'
                }
            ],
            order: [[2, 'asc']] // Order by nama column (ascending)
        });
    });

    $("#btn-export").click(function() {
        var table = $('#datatable').DataTable();
        var data = table.ajax.params();
        data.excel = 1;
        data.params = JSON.stringify(data);
        delete data.draw;
        delete data.start;
        delete data.length;
        delete data._;

        var url = $(this).data('href') + '?' + $.param(data);
        window.open(url, '_blank');
    });
</script>
@endpush