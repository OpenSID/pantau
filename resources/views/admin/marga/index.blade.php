@extends('layouts.index')
@include('layouts.components.select2_wilayah')
@section('title', 'Daftar Marga')

@section('content_header')
    <h1>Daftar Marga Indonesia</h1>
@stop

@section('content')
    @include('layouts.components.notification')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                        aria-expanded="false" aria-controls="collapse-filter">
                        <i class="fas fa-filter"></i>
                    </a>
                    <a href="{{ route('marga.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>
                        &ensp;Tambah</a>
                    <a href="{{ route('marga.import') }}" class="btn btn-primary btn-sm"><i class="fas fa-upload"></i>
                        &ensp;Import</a>
                    <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('marga') }}"><i
                            class="fas fa-file-excel"></i> Excels<a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.components.form_filter')
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="5%" nowrap>NO</th>
                                    <th width="5%" nowrap>AKSI</th>
                                    <th>NAMA</th>
                                    <th>SUKU</th>
                                    <th>NAMA PROVINSI</th>
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
            const margaTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                searchable: true,
                orderable: true,
                ajax: {
                    url: "{{ url('marga') }}",
                    data: function(d) {
                        d.kode_provinsi = $('#provinsi').val();
                        d.suku = $('#suku').val();
                    }
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
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'suku.name',
                        name: 'suku.name'
                    },
                    {
                        data: 'suku.region.region_name',
                        name: 'suku.region.region_name',
                        searchable: false,
                        orderable: false
                    },
                ],
                order: [
                    [2, 'asc']
                ],
            });

            $('#filter').on('click', function(e) {
                margaTable.draw();
            });

            $(document).on('click', '#reset', function(e) {
                e.preventDefault();
                $('#provinsi').val('').change();
                $('#suku').val('').change();
                margaTable.ajax.reload();
            });

            $('#btn-export').click(function() {
                const _href = $(this).data('href')
                window.location.href = _href + '?excel=1&params=' + JSON.stringify($('#datatable')
                    .DataTable().ajax
                    .params())
            })
        });
    </script>
@endsection
