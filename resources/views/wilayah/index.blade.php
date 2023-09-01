@extends('layouts.index')

@section('title', 'Wilayah Administratif')

@section('content_header')
    <h1>Wilayah Administratif<small class="font-weight-light ml-1 text-md">(Permendagri No. 77 Tahun 2019)</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Desa</th>
                                    <th>Kode Bps Desa</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
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
        var desa = $('#table-desa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
            ordering: true,
            ajax: {
                url: `{{ url('wilayah') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'kode_desa'
                },
                {
                    data: 'bps_kemendagri_desa.kode_desa_bps',
                    name: 'bpsKemendagriDesa.kode_desa_bps',
                    defaultContent: ''
                },
                {
                    data: 'nama_desa'
                },
                {
                    data: 'nama_kec'
                },
                {
                    data: 'nama_kab'
                },
                {
                    data: 'nama_prov'
                },

            ],
            order: [
                [1, 'asc']
            ],
        })
    </script>
@endsection
