@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Desa Pengguna Aplikasi Mobile<small class="font-weight-light ml-1 text-md font-weight-bold"> @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
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
                        <table class="table" id="table-versi">
                            <thead>
                                <tr>
                                    <th>Kode Desa</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Jumlah</th>
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

    if (params.get('akses_mobile') || params.get('versi_mobile')) {
        if (params.get('akses_mobile')) {
            $('#akses_mobile').val(params.get('akses_mobile')).change();
        }

        filter_open();
    }

    var desa = $('#table-versi').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ordering: true,
        ajax: {
            url: `{{ url('mobile/desa') }}`,
            method: 'get',
            data: function(data) {
                    data.kode_provinsi = $('#provinsi').val();
                    data.kode_kabupaten = $('#kabupaten').val();
                    data.akses_mobile = $('#akses_mobile').val();
                    data.versi_mobile = $('#versi_mobile').val();
                }
        },
        columns: [
            {
                data: 'kode_desa',
                orderable: false
            },
            {
                data: 'nama_desa',
                orderable: false
            },
            {
                data: 'nama_kecamatan',
                orderable: false
            },
            {
                data: 'nama_kabupaten',
                orderable: false
            },
            {
                data: 'nama_provinsi',
                orderable: false
            },
            {
                orderable: false,
                data: function (data) {
                        return `<a target="_blank" href="{{ url('mobile/pengguna') }}?kode_provinsi=${data.kode_provinsi}&kode_kabupaten=${data.kode_kabupaten}&kode_kecamatan=${data.kode_kecamatan}&kode_desa=${data.kode_desa}">${data.jumlah}</a>`
                    },
            },

        ],
    })

    $('#filter').on('click', function(e) {
        desa.draw();
    });

    $(document).on('click', '#reset', function(e) {
        e.preventDefault();
        $('#provinsi').val('').change();
        $('#kabupaten').val('').change();
        $('#akses_mobile').val('0').change();

        kabupaten.ajax.reload();
    });

</script>
@endsection
