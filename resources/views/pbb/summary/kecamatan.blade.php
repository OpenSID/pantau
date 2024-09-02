<div class="card card-outline card-info col-lg-12">
    <div class="card-header">
        <h3 class="card-title">Kecamatan Pengguna</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            @foreach ($kecamatanWidgets as $widget)
                @include('widget.card', $widget)
            @endforeach
        </div>

        <div class="card card-outline card-info col-lg-12">
            <div class="card-header">
                <h3 class="card-title">Kecamatan baru dalam 7 hari terakhir</h3>
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
                    <table class="table" id="table-kec-baru">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tgl Terpantau</th>
                                <th>Kecamatan</th>
                                <th>Kabupaten</th>
                                <th>Provinsi</th>
                                <th>Web</th>
                                <th>Versi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar_baru as $key => $item)
                                <tr>
                                    <td>{{ $key +1 }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->nama_kecamatan }}</td>
                                    <td>{{ $item->nama_kabupaten }}</td>
                                    <td>{{ $item->nama_kabupaten }}</td>
                                    <td>{{ $item->url }}</td>
                                    <td>{{ $item->versi }}</td>
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
