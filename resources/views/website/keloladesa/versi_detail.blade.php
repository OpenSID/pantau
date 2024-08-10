@extends('layouts.web')
@include('layouts.components.select2_wilayah')

@section('title', 'KelolaDesa')

@section('content')        
    <div class="row">
    <h1>
        KelolaDesa<small class="font-weight-light ml-1 text-md font-weight-bold">(Desa yang memasang KelolaDesa)
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
                        <table class="table" id="table-keloladesa">
                            <thead>
                                <tr>
                                    <th>No</th>                                    
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

        var desa = $('#table-keloladesa').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,

                ajax: {
                    url: `{{ url('web/keloladesa/versi/detail') }}`,
                    method: 'get',
                    data: function(data) {
                        data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                            'kode_provinsi');
                        data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                            'kode_kabupaten');
                        data.kode_kecamatan = $('#kecamatan').val();
                        data.status = $('#status').val();
                        data.versi = params.get('versi')                        
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                    data: 'desa.nama_desa'
                },
                {
                    data: 'desa.nama_kecamatan'
                },
                {
                    data: 'desa.nama_kabupaten'
                },
                {
                    data: 'desa.nama_provinsi'
                },
                {
                    data: 'jumlah',
                    searchable: false,
                    orderable: false,                    
                },
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

            desa.ajax.reload();
        });
    </script>
@endsection