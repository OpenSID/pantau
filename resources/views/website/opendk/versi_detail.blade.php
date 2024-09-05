@extends('layouts.web')
@include('layouts.components.select2_wilayah')

@section('title', 'OpenDK')

@section('content')        
    <div class="row">
    <h1>
        OpenDK<small class="font-weight-light ml-1 text-md font-weight-bold">(Kecamatan yang memasang OpenDK)
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
                        <table class="table" id="table-opendk">
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
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>   
        const params = new URLSearchParams(window.location.search);             
        var opendk = $('#table-opendk').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,

                ajax: {
                    url: `{{ url('web/opendk/versi/detail') }}`,
                    method: 'get',
                    data: function(data) {
                        data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get(
                            'kode_provinsi');
                        data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get(
                            'kode_kabupaten');
                        data.kode_kecamatan = $('#kecamatan').val();                        
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
                    data: 'nama_kecamatan'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'updated_at',
                    searchable: false,                                      
                },
                ],            
        
        });

        $('#filter').on('click', function(e) {
            opendk.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#provinsi').val('').change();
            $('#kabupaten').val('').change();
            $('#kecamatan').val('').change();                     

            opendk.draw();
        });
    </script>
@endsection