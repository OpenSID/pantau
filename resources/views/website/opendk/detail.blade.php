@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor')

@section('content_header')

@stop

@section('content')
@include('layouts.components.daterangepicker')
<div class="row">
    <div class="col-12">
        <div class=" bg-gray-light">
            <div class="card-body">
                <div class="row">
                    <h1>Pengguna Aplikasi OpenDK</small></h1>
                    <div class="col-lg-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-sm btn-secondary" data-toggle="collapse"
                                            href="#collapse-filter" role="button" aria-expanded="false"
                                            aria-controls="collapse-filter">
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
                                <div class="table-responsive">
                                    <table class="table" id="table-pengguna-opendk">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kecamatan</th>
                                                <th>Kabupaten</th>
                                                <th>Provinsi</th>
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
</div>
@stop


@push('js')
<script>
    $(document).ready(function() {
        const filters = {!! json_encode(request()->all()) !!};

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
            $('#akses').find('option[value="5"]').remove();

        $('#table-pengguna-opendk').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:pengguna-opendk') }}`,
                method: 'get',
                data: function(data) {
                    data.kode_provinsi = $('#provinsi').val();
                    data.kode_kabupaten = $('#kabupaten').val();
                    data.kode_kecamatan = $('#kecamatan').val();
                    data.akses = $('#akses').val();
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
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'updated_at'
                },
            ]
        })
        
        $('#filter').on('click', function(e) {
            // e.preventDefault();
            $('#table-pengguna-opendk').DataTable().draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();
            $('#table-pengguna-opendk').DataTable().draw();
        });
    })
</script>
@endpush