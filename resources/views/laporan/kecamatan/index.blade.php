@extends('layouts.index')
@include('layouts.components.select2_wilayah')
@section('title', 'Kecamatan OpenSID')

@section('content_header')
    <h1>Kecamatan OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Kecamatan yang memasang OpenSID) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                                aria-expanded="false" aria-controls="collapse-filter">
                                <i class="fas fa-filter"></i>
                            </a>
                            <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('laporan/kecamatan') }}"><i class="fas fa-file-excel"></i> Excels<a>
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
                        <table class="table" id="table-kecamatan">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Total Desa</th>
                                    <th>Server Offline</th>
                                    <th>Server Online</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const params = new URLSearchParams(window.location.search);

        switch (params.get('status')) {
            case '1':
                $('#status').val('1').change();
                filter_open();
                break;
            case '2':
                $('#status').val('2').change();
                filter_open();
                break;
            case '3':
                $('#status').val('3').change();
                filter_open();
                break;
            default:
                break;
        }

        // Set provinsi dan kabupaten dari parameter
        if (params.get('kode_provinsi')) {
            $('#provinsi').val(params.get('kode_provinsi')).change();
            filter_open();
        }
        if (params.get('kode_kabupaten')) {
            $('#kabupaten').val(params.get('kode_kabupaten')).change();
            filter_open();
        }

        var kecamatan = $('#table-kecamatan').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/kecamatan') }}`,
                method: 'get',
                data: function(data) {
                    data.status = $('#status').val();
                    data.kode_provinsi = $('#provinsi').val();
                    data.kode_kabupaten = $('#kabupaten').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'nama_kecamatan',
                    name: 'sub.nama_kecamatan',
                },
                {
                    data: 'nama_kabupaten',
                    name: 'sub.nama_kabupaten',
                },
                {
                    data: 'nama_provinsi',
                    name: 'sub.nama_provinsi',
                },
                {
                    data: 'total_desa',
                    name: 'total_desa',
                    searchable: false,
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=${$('#status').val() == 3? 3: 2}&kode_kecamatan=${data.kode_kecamatan}">${data.offline}</a>`
                    },
                    searchable: false,
                    name: 'offline',
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=${$('#status').val() == 3? 3: 1}&kode_kecamatan=${data.kode_kecamatan}">${data.online}</a>`
                    },
                    searchable: false,
                    name: 'online',
                },
            ]
        });

        $('#filter').on('click', function(e) {
            kecamatan.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#status').val('0').change();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();

            kecamatan.ajax.reload();
        });

        $('#btn-export').click(function(){
            const _href = $(this).data('href')
            window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#table-kecamatan').DataTable().ajax.params())
        })
    </script>
@endsection
