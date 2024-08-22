@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa Aktif OpenSID')

@section('content_header')
    <h1>
        Desa OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Desa Aktif OpenSID) @if ($provinsi = session('provinsi'))
                {{ "| {$provinsi->nama_prov}" }}
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
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>                                    
                                    <th>Desa</th>
                                    <th>Akses Publik selama 30 hari</th>
                                    <th>Akses Admin selama 30 hari</th>
                                    <th>Total Surat Tercetak</th>
                                    <th>Jumlah Pengguna Layanan Mandiri</th>
                                    <th>Jumlah Artikel</th>
                                    <th>Jumlah Mutasi Penduduk</th>
                                    <th>Jumlah Dokumen</th>
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
                $('#status').val('2').change()
                filter_open();
                break;

            case '3':
                $('#status').val('3').change()
                filter_open();
                break;

            default:
                break;
        }

        switch (params.get('akses')) {
            case '4':
                $('#akses').val('4').change();
                filter_open();
                break;
            case '5':
                $('#akses').val('5').change();
                filter_open();
                break;

            default:
                break;
        }

        var desa = $('#table-desa').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,

                ajax: {
                    url: `{{ url('laporan/desa-aktif') }}`,
                    method: 'get',
                    data: function(data) {
                        data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                            'kode_provinsi');
                        data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                            'kode_kabupaten');
                        data.kode_kecamatan = $('#kecamatan').val();
                        data.status = $('#status').val();
                        data.akses = $('#akses').val();
                        data.tte = $('#tte').val();
                        data.versi_lokal = params.get('versi_lokal');
                        data.versi_hosting = params.get('versi_hosting');
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                {
                    data: 'nama_desa'
                },
                {
                    data: 'akses_count'
                },
                {
                    data: 'akses_count'
                },
                {
                    data: 'jml_surat_tte'
                },
                {
                    data: 'jml_mandiri',
                    searchable: false,
                },
                {
                    data: 'jml_artikel',
                    searchable: false,
                },                               
                {
                    data: 'jml_mutasi_penduduk',
                    searchable: false,
                    defaultContent: '-'
                },
                {
                    data: 'jml_dokumen',
                    searchable: false,
                } ],
            order: [
                [2, 'desc']
            ],
        });

        $('#filter').on('click', function(e) {
            desa.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#status').val('0').change();
            $('#akses').val('0').change();
            $('#tte').val('empty').change();

            desa.ajax.reload();
        });
    </script>
@endsection
