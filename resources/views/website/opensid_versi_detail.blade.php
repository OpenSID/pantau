@extends('layouts.web')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content')        
    <div class="row">
    <h1>
        Desa OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Desa yang memasang OpenSID)
        </small></h1>
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
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Versi Offline</th>
                                    <th>Versi Online</th>
                                    <th>Modul TTE</th>
                                    <th>Surat ter-TTE</th>                                    
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
                    url: `{{ url('laporan/desa') }}`,
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
                    data: 'nama_kecamatan'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: function(data) {
                        if (data.url_hosting) {
                            return `<a target="_blank" href="https://${data.url_hosting}">https://${data.url_hosting}</a>`
                        } else if (data.url_lokal) {
                            return `<a target="_blank" href="http://${data.url_lokal}">http://${data.url_lokal}</a>`
                        }

                        return '';
                    },
                    searchable: false,
                    orderable: false,
                    visible: {{ auth()->check() == false ? 'false' : 'true' }}
                },
                {
                    data: 'versi_lokal'
                },

                {
                    data: 'versi_hosting'
                },
                {
                    data: function(data) {
                        if (data.modul_tte == 1) {
                            return `<span class="badge badge-pill badge-info">Aktif</span>`
                        } else {
                            return `<span class="badge badge-pill badge-secondary">Tidak Aktif</span>`
                        }
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'jml_surat_tte',
                    searchable: false,
                },
                 {
                data: 'tgl_akses',
                searchable: false,
            }, ],
            
            order: [
                [10, 'desc']
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
