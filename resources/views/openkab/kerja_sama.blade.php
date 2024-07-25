@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Daftar Wilayah Kerja Sama</h1>
@stop

@section('content')
    @include('layouts.components.notification')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="btn btn-sm btn-primary" href="{{ route('openkab.getwilayah') }}">Perbarui Data Wilayah</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table-kerja-sama">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Kabupaten</th>
                                    <th>Nama Kabupaten</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    var kecamatan = $('#table-kerja-sama').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('openkab/kerja-sama') }}`,
                method: 'get',
                data: function(data) {
                    
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'kode_kab',
                    name: 'kode_kab'
                },
                {
                    data: 'nama_kab',
                    name: 'nama_kab'
                },
            ]
        })
</script>
@endsection
