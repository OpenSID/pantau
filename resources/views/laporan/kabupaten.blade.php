@extends('layouts.index')

@section('title', 'Kabupaten OpenSID')

@section('content_header')
    <h1>Kabupaten OpenSID<small class="font-weight-light ml-1 text-md font-weight-bold">(Kabupaten yang memasang OpenSID) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')
    @include('layouts.components.global_delete')
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
                        <table class="table" id="table-kabupaten">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Server Offline</th>
                                    <th>Server Online</th>
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
        $('#status').select2();

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

        var desa = $('#table-kabupaten').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/kabupaten') }}`,
                method: 'get',
                data: function(data) {
                    data.status = $('#status').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'nama_kabupaten',
                    name: 'sub.nama_kabupaten',
                },
                {
                    data: 'nama_provinsi',
                    name: 'sub.nama_provinsi',
                },
                {
                    data: function (data) {

                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=${$('#status').val() == 3? 3: 2}&kode_kabupaten=${data.kode_kabupaten}&kode_provinsi=${data.kode_provinsi}">${data.offline}</a>`
                    },
                    searchable: false,
                    name: 'offline',
                },
                {
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?status=${$('#status').val() == 3? 3: 2}&kode_kabupaten=${data.kode_kabupaten}&kode_provinsi=${data.kode_provinsi}">${data.online}</a>`
                    },
                    searchable: false,
                    name: 'online',
                },
            ]
        });

        $('#filter').on('click', function(e) {
            desa.draw();
        });

        $(document).on('click', '#reset', function(e) {
            e.preventDefault();
            $('#status').val('0').change();

            desa.ajax.reload();
        });
    </script>
@endsection
