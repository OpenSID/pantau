@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Kabupaten OpenDK<small class="font-weight-light ml-1 text-md font-weight-bold">(Kabupaten yang memasang OpenDK ) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
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
                                    <th>Kode Kabupaten</th>
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
    const listVersi = {!! json_encode($listVersi) !!}

    for(var i in listVersi) {
        $('#versi_opendk').append('<option>'+listVersi[i]+'</option>')
    }

    if (params.get('akses_opendk') || params.get('versi_opendk')) {
        if (params.get('versi_opendk')) {
            $('#versi_opendk').val(params.get('versi_opendk')).change();
        }
        if (params.get('akses_opendk')) {
            $('#akses_opendk').val(params.get('akses_opendk')).change();
        }

        filter_open();
    }

    var kabupaten = $('#table-versi').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ordering: true,
        ajax: {
            url: `{{ url('opendk/kabupaten') }}`,
            method: 'get',
            data: function(data) {
                    data.kode_provinsi = $('#provinsi').val();
                    data.kode_kabupaten = $('#kabupaten').val();
                    data.akses_opendk = $('#akses_opendk').val();
                    data.versi_opendk = $('#versi_opendk').val();
                }
        },
        columns: [
            {
                data: 'kode_kabupaten',
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
                data: 'jumlah',
                orderable: false
            },

        ],
    })

    $('#filter').on('click', function(e) {
        kabupaten.draw();
    });

    $(document).on('click', '#reset', function(e) {
        e.preventDefault();
        $('#provinsi').val('').change();
        $('#kabupaten').val('').change();
        $('#versi_opendk').val('0').change();
        $('#akses_opendk').val('0').change();

        kabupaten.ajax.reload();
    });

</script>
@endsection
