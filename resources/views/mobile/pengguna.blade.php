@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Pengguna Aplikasi LayananDesa<small class="font-weight-light ml-1 text-md font-weight-bold">@if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
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
                                    <th>Id</th>
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
            url: `{{ url('mobile/pengguna') }}`,
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
                data: 'id',
                orderable: false
            },
            {
                data: 'tgl_akses',
                orderable: true
            },
            {
                data: 'desa.nama_desa',
                orderable: false
            },
            {
                data: 'desa.nama_kecamatan',
                orderable: false
            },
            {
                data: 'desa.nama_kabupaten',
                orderable: false
            },
            {
                data: 'desa.nama_provinsi',
                orderable: false
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

</script>
@endsection
