<div class="card card-outline card-info col-lg-12">
    <div class="card-header">
        <h3 class="card-title">Pengguna Aplikasi Mobile</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            @foreach ($desaWidgets as $widget)
                @include('widget.card', $widget)
            @endforeach
        </div>

        <div class="card card-outline card-info col-lg-12">
            <div class="card-header">
                <h3 class="card-title">Pengguna baru dalam 7 hari terakhir</h3>
                <div class="card-tools">
                    <span data-toggle="tooltip" title="{{ count($daftar_baru) }} Desa Baru"
                        class="badge badge-primary">{{ count($daftar_baru) }}</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="table-mobile-baru">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Id</th>
                                <th>Tgl Terpantau</th>
                                <th>Desa</th>
                                <th>Kecamatan</th>
                                <th>Kabupaten</th>
                                <th>Provinsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar_baru as $key => $item)
                                <tr>
                                    <td>{{ $key +1 }}</td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->desa->nama_desa }}</td>
                                    <td>{{ $item->desa->nama_kecamatan }}</td>
                                    <td>{{ $item->desa->nama_kabupaten }}</td>
                                    <td>{{ $item->desa->nama_provinsi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <!-- /.card-body -->
</div>

@section('js')
    <script>
        $('#table-mobile-baru').DataTable();

    </script>
@stop
