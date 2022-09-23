@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Desa OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Desa yang memasang OpenSID) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
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
                            <div id="collapse-filter" class="collapse">
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Provinsi</label>
                                            <select class="select2 form-control-sm" id="provinsi" name="provinsi"
                                                data-placeholder="Semua Provinsi" style="width: 100%;">
                                                <option value="" selected>Semua Provinsi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Kabupaten</label>
                                            <select class="select2 form-control-sm" id="kabupaten" name="kabupaten"
                                                data-placeholder="Semua Kabupaten" style="width: 100%;" disabled>
                                                <option value="" selected>Semua Kabupaten</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Kecamatan</label>
                                            <select class="select2 form-control-sm" id="kecamatan" name="kecamatan"
                                                data-placeholder="Semua Kecamatan" style="width: 100%;" disabled>
                                                <option value="" selected>Semua Kecamatan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Jenis Server</label>
                                            <select class="select2 form-control-sm" id="status" name="online"
                                                data-placeholder="Semua Status" style="width: 100%;">
                                                <option selected value="0">Semua Status</option>
                                                <option value="1">Online</option>
                                                <option value="2">Offline</option>
                                                <option value="3">Premium Terbaru</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Akses Terakhir</label>
                                            <select class="select2 form-control-sm" id="akses" name="akses"
                                                data-placeholder="Semua Status" style="width: 100%;">
                                                <option selected value="0">Semua Status</option>
                                                <option value="5">Desa aktif hanya offline</option>
                                                <option value="4">Sejak tujuh hari yang lalu</option>
                                                <option value="2">Sejak dua bulan yang lalu</option>
                                                <option value="1">Sebelum dua bulan yang lalu</option>
                                                <option value="3">Sebelum empat bulan yang lalu</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label>Modul TTE</label>
                                            <select class="select2 form-control-sm" id="tte" name="tte"
                                                data-placeholder="Semua Status" style="width: 100%;">
                                                <option selected value="empty">Semua Status</option>
                                                <option value="1">Modul TTE Aktif</option>
                                                <option value="0">Modul TTE Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="btn-group btn-group-sm btn-block">
                                                    <button type="button" id="reset" class="btn btn-secondary"><span
                                                            class="fas fa-ban"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="btn-group btn-group-sm btn-block">
                                                    <button type="button" id="filter" class="btn btn-primary"><span
                                                            class="fas fa-search"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-0">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @auth
                                        <th>Aksi</th>
                                    @endauth
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
                $('#status').val('1').change()
                break;
            case '2':
                $('#status').val('2').change()
                break;

            case '3':
                $('#status').val('3').change()
                break;

            default:
                break;
        }

        switch (params.get('akses')) {
            case '4':
                $('#akses').val('4').change()
                break;
            case '5':
                $('#akses').val('5').change()
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
                    data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get('kode_provinsi');
                    data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get('kode_kabupaten');
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
                @auth
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                },
                @endauth
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
                    data: function (data) {
                        if (data.url_hosting) {
                            return `<a target="_blank" href="https://${data.url_hosting}">https://${data.url_hosting}</a>`
                        } else if (data.url_lokal) {
                            return `<a target="_blank" href="http://${data.url_lokal}">http://${data.url_lokal}</a>`
                        }

                        return '';
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'versi_lokal'
                },

                {
                    data: 'versi_hosting'
                },
                {
                    data: function (data) {
                        if (data.tte == 1) {
                            return `<span class="badge badge-pill badge-info">Aktif</span>`
                        } else  {
                            return `<span class="badge badge-pill badge-secondary">Tidak Aktif</span>`
                        }
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'surat_tte',
                    searchable: false,
                },
                {
                    data: 'tgl_akses',
                    searchable: false,
                },
            ],
            @auth
            order: [
                [11, 'desc']
            ],
            @else
            order: [
                [10, 'desc']
            ],
            @endauth
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
