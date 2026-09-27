@extends('layouts.index')
@include('layouts.components.select2_wilayah')

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
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{ route('kecamatan.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>
                                &ensp;Tambah</a>
                            <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('kecamatan') }}"><i class="fas fa-file-excel"></i> Excel</a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                                aria-expanded="false" aria-controls="collapse-filter">
                                <i class="fas fa-filter"></i> Filter
                            </a>
                        </div>
                    </div>
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
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            searchable: true,
            orderable: true,
            ajax: {
                url: "{{ url('kecamatan') }}",
                data: function(d) {
                    d.kode_provinsi  = $('#provinsi').val();
                    d.kode_kabupaten = $('#kabupaten').val();
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

        $('#filter').on('click', function() {
            table.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').trigger('change');
            $('#kabupaten').val('').trigger('change').attr('disabled', true);
            $('#kecamatan').val('').trigger('change').attr('disabled', true);
            table.draw();
        });

        $('#btn-export').click(function() {
            const _href = $(this).data('href')
            window.location.href = _href + '?excel=1&params=' + JSON.stringify($('#datatable').DataTable().ajax.params())
        })
    </script>
@endsection
