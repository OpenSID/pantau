@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Kecamatan Aktif OpenDK')

@section('content_header')
<h1>
    Kecamatan Aktif OpenDK<small
        class="font-weight-light ml-1 text-md font-weight-bold">@if ($provinsi = session('provinsi'))
            | {{ $provinsi->nama_prov ?? $provinsi }}
        @endif
    </small></h1>
@stop

@section('content')    
    @include('layouts.components.notification')
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
                        <table class="table" id="table-kecamatan-aktif">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Jumlah Desa</th>
                                    <th>Akses Publik Selama 30 Hari</th>
                                    <th>Akses Admin Selama 30 Hari</th>
                                    <th>Jumlah Artikel</th>
                                    <th>Akses Terakhir</th>
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

        switch (params.get('akses_opendk')) {
            case '1':
                $('#akses').val('1').change();
                filter_open();
                break;
            case '2':
                $('#akses').val('2').change()
                filter_open();
                break;
            case '3':
                $('#akses').val('3').change()
                filter_open();
                break;
            default:
                break;
        }

        var kecamatanAktif = $('#table-kecamatan-aktif').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,

            ajax: {
                url: `{{ url('opendk/kecamatan-aktif') }}`,
                method: 'get',
                data: function (data) {
                    data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                        'kode_provinsi');
                    data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                        'kode_kabupaten');
                    data.kode_kecamatan = $('#kecamatan').val();
                    data.akses_opendk = $('#akses').val();
                    data.versi_opendk = params.get('versi_opendk');
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'nama_kecamatan'
            },
            {
                data: 'jumlah_desa'
            },
            {
                data: 'akses_publik_30_hari'
            },
            {
                data: 'akses_admin_30_hari'
            },
            {
                data: 'jumlah_artikel'
            },
            {
                data: 'akses_terakhir'
            }],
            order: [
                [3, 'desc']
            ],
        });

        $('#filter').on('click', function (e) {
            kecamatanAktif.draw();
        });

        $(document).on('click', '#reset', function (e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#akses').val('').change();

            kecamatanAktif.ajax.reload();
        });
    </script>
@endsection