@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor')

@section('content_header')

@stop

@section('content')
    @include('layouts.components.daterangepicker')
    <div class="col-12">
        <div class=" bg-gray-light">
            <div class="card-body">
                <div class="row">
                    <h1>Pengguna Aplikasi OpenSID</small></h1>
                    <div class="col-lg-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter"
                                            role="button" aria-expanded="false" aria-controls="collapse-filter">
                                            <i class="fas fa-filter"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('layouts.components.form_filter')
                                    </div>
                                </div>
                                <div id="periode-akses-info" class="alert alert-info py-2 d-flex justify-content-between align-items-center" style="display: none !important;">
                                    <span>
                                        <i class="fas fa-calendar"></i>
                                        Menampilkan desa dengan akses terakhir pada periode
                                        <strong id="periode-akses-label"></strong>
                                    </span>
                                    <button type="button" class="close" id="hapus-periode-akses" aria-label="Hapus filter periode">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table" id="table-pengguna-opensid">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Desa</th>
                                                <th>Kecamatan</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
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
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
@stop


@push('js')
    <script>
        $(document).ready(function() {
            const filters = {!! json_encode(request()->all()) !!};
            // Periode akses dari kartu "Desa Aktif" di dasbor (sudah divalidasi di controller)
            let periodeAkses = @json($fillters['period'] ?? null);

            function tampilkanPeriodeAkses() {
                if (periodeAkses) {
                    $('#periode-akses-label').text(periodeAkses);
                    $('#periode-akses-info').attr('style', '');
                } else {
                    $('#periode-akses-label').text('');
                    $('#periode-akses-info').attr('style', 'display: none !important;');
                }
            }

            function hapusPeriodeAkses() {
                periodeAkses = null;
                tampilkanPeriodeAkses();
            }

            tampilkanPeriodeAkses();

            // Tunggu select2 selesai diinisialisasi
            setTimeout(function() {
                if (filters.kode_provinsi) {
                    let option = new Option(filters.nama_provinsi, filters.kode_provinsi, true, true);
                    $('#provinsi').append(option).trigger('change');

                    // Set kabupaten setelah delay
                    if (filters.kode_kabupaten) {
                        let optionKab = new Option(filters.nama_kabupaten, filters.kode_kabupaten, true, true);
                        $('#kabupaten').attr('disabled', false);
                        $('#kabupaten').append(optionKab).trigger('change');
                    }

                    if (filters.kode_kecamatan) {
                        let optionKec = new Option(filters.nama_kecamatan, filters.kode_kecamatan, true, true);
                        $('#kecamatan').attr('disabled', false);
                        $('#kecamatan').append(optionKec).trigger('change');
                    }                                        
                }
                if(filters.akses){
                    $('#akses').val(filters.akses).trigger('change');
                }
                $('#filter').trigger('click');
            }, 1000);
            const semuaDesa = $('#table-pengguna-opensid').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                order: [[9, 'desc']], // Default ordering by column 9 (Akses Terakhir) descending
                ajax: {
                    url: `{{ route('datatables:pengguna-opensid') }}`,
                    data: function(d) {
                        d.kode_provinsi = $('#provinsi').val();
                        d.kode_kabupaten = $('#kabupaten').val();
                        d.kode_kecamatan = $('#kecamatan').val();
                        d.status = $('#status').val();
                        d.akses = $('#akses').val();
                        d.tte = $('#tte').val();
                        d.period = periodeAkses || '';
                    },
                    method: 'get',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'nama_desa',
                        name: 'nama_desa'
                    },
                    {
                        data: 'nama_kecamatan',
                        name: 'nama_kecamatan'
                    },
                    {
                        data: 'nama_kabupaten',
                        name: 'nama_kabupaten'
                    },
                    {
                        data: 'nama_provinsi',
                        name: 'nama_provinsi'
                    },
                    {
                        data: 'versi_lokal',
                        name: 'versi_lokal'
                    },
                    {
                        data: 'versi_hosting',
                        name: 'versi_hosting'
                    },
                    {
                        data: 'modul_tte',
                        name: 'modul_tte',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'jml_surat_tte',
                        name: 'jml_surat_tte'
                    },
                    {
                        data: 'tanggal',
                        name: 'updated_at',
                        searchable: false,
                        orderable: true
                    },
                ]
            })

            $('#filter').click(function() {
                semuaDesa.ajax.reload();
            })
            $('#reset').click(function() {
                $('#kode_provinsi').val('').trigger('change');
                $('#kode_kabupaten').val('').trigger('change');
                $('#kode_kecamatan').val('').trigger('change');
                $('#status').val('').trigger('change');
                $('#akses').val('').trigger('change');
                $('#tte').val('').trigger('change');
                hapusPeriodeAkses();
                semuaDesa.ajax.reload();
            })
            $('#hapus-periode-akses').click(function() {
                hapusPeriodeAkses();
                semuaDesa.ajax.reload();
            })
            // Memilih filter Akses secara manual menggantikan periode dari dasbor agar filter tidak bertumpuk
            $('#akses').on('select2:select', function() {
                hapusPeriodeAkses();
            })
        })
    </script>
@endpush
