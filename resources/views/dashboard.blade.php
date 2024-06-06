@extends('layouts.web')

@section('title', 'Dasbor')

@section('content_header')
    <h1>Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan OpenSID @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')    
    
@stop

@section('js')
    <script>
        var desaBaru = $('#table-desa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:desa-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'format_created_at', name: 'created_at'
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
                    data: function (data) {
                        if (data.url_hosting) {
                            return `<a target="_blank" href="https://${data.url_hosting}">https://${data.url_hosting}</a>`
                        } else if (data.url_lokal) {
                            return `<a target="_blank" href="http://${data.url_lokal}">http://${data.url_lokal}</a>`
                        }

                        return '';
                    },
                    searchable: false,
                    orderable: false,
                    visible : {{ auth()->check() == false ?'false' : 'true' }}
                },
                {
                    data: 'versi',
                    searchable: false
                },
            ],
            order: [
                [1, 'desc']
            ],
        })

        var kabupatenKosong = $('#table-kabupaten-kosong').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:kabupaten-kosong') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'region_code'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'jml_desa'
                },
            ]
        })
    </script>
@stop
