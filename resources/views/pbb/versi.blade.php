@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa PBB')

@section('content_header')
    <h1>Profil Versi PBB<small class="font-weight-light ml-1 text-md font-weight-bold">(Versi yang terpasang ) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
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
                                    <th>No</th>
                                    <th>Versi</th>
                                    <th>Url</th>

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
    const listVersi = {!! json_encode($listVersi) !!}

    for(var i in listVersi) {
        $('#versi_opendk').append('<option>'+listVersi[i]+'</option>')
    }

    var kecamatan = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('pbb/versi') }}`,
                method: 'get',
                data: function(data) {
                    data.versi_opendk = $('#versi_opendk').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'versi',
                    orderable: false
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('pbb/kecamatan') }}?versi_opendk=${data.versi_clean}">${data.jumlah}</a>`
                    }
                },

            ]
        })

    $('#filter').on('click', function(e) {
        kecamatan.draw();
    });

    $(document).on('click', '#reset', function(e) {
        e.preventDefault();
        $('#versi_opendk').val('0').change();

        kecamatan.ajax.reload();
    });
</script>
@endsection
