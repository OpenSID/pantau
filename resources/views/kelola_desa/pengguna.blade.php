@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Pengguna Aplikasi KelolaDesa<small class="font-weight-light ml-1 text-md font-weight-bold">@if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
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
                            <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('kelola_desa/pengguna') }}"><i class="fas fa-file-excel"></i> Excels<a>
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
                        <table class="table" id="table-pengguna">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tgl Terpantau</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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

    if (params.get('akses_mobile') || params.get('versi_mobile')) {
        if (params.get('akses_mobile')) {
            $('#akses_mobile').val(params.get('akses_mobile')).change();
        }

        filter_open();
    }

    var pengguna = $('#table-pengguna').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ordering: true,
        ajax: {
            url: `{{ url('kelola_desa/pengguna') }}`,
            method: 'get',
            data: function(data) {
                    data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get('kode_provinsi');
                    data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get('kode_kabupaten');
                    data.kode_kecamatan = $('#kecamatan').val()? $('#kecamatan').val() : params.get('kode_kecamatan');
                    data.kode_desa = $('#desa').val()? $('#desa').val() : params.get('kode_desa');
                    data.akses_mobile = $('#akses_mobile').val();
                }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'tgl_akses',
                orderable: true
            },
            {
                data: 'nama_desa',
                orderable: true
            },
            {
                data: 'nama_kecamatan',
                orderable: true
            },
            {
                data: 'nama_kabupaten',
                orderable: true
            },
            {
                data: 'nama_provinsi',
                orderable: true
            },
        ],
        orders: [2, 'desc']
    })

    $('#filter').on('click', function(e) {
        pengguna.draw();
    });

    $(document).on('click', '#reset', function(e) {
        e.preventDefault();
        $('#provinsi').val('').change();
        $('#kabupaten').val('').change();
        $('#kecamatan').val('').change();
        $('#desa').val('').change();
        $('#akses_mobile').val('0').change();

        pengguna.ajax.reload();
    });
    $('#btn-export').click(function(){
        const _href = $(this).data('href')
        window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#table-pengguna').DataTable().ajax.params())
    })
</script>
@endsection
