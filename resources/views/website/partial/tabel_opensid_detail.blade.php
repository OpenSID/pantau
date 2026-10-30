<div class="card mt-3">
    <div class="card-body">
        @if(auth()->check())
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="text-white">Kode Desa</label>
                    <input type="text" class="form-control form-control-sm" id="filter-kode-desa"
                        placeholder="Contoh: 3201230012">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="text-white">Domain</label>
                    <input type="text" class="form-control form-control-sm" id="filter-domain"
                        placeholder="Cari domain">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="text-white">Jumlah Domain</label>
                    <select class="form-control form-control-sm" id="filter-jumlah-domain">
                        <option value="">Semua</option>
                        <option value="1">Lebih dari 1 domain</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="text-white">Riwayat Domain</label>
                    <select class="form-control form-control-sm" id="filter-punya-riwayat">
                        <option value="">Semua</option>
                        <option value="1">Memiliki riwayat</option>
                        <option value="0">Tanpa riwayat</option>
                    </select>
                </div>
            </div>
        </div>
        @endif
        <div class="table-responsive">
            <table class="table" id="table-semua-desa">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Terpantau</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Versi</th>
                        <th>Modul TTE</th>
                        <th>Jenis Server</th>
                        @if(auth()->check())
                        <th>Domain Saat Ini</th>
                        <th>Riwayat Domain</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('layouts.components.select2_wilayah_modal')
@push('js')
    <script>
        $.extend($.fn.dataTable.defaults, {
            language: { url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json" }
        });

        var semuaDesa = $('#table-semua-desa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:semua-desa') }}`,
                method: 'get',
                data: function (d) {
                    d.kode_provinsi = $('#kode_provinsi').val();
                    d.kode_kabupaten = $('#kode_kabupaten').val();
                    d.kode_kecamatan = $('#kode_kecamatan').val();
                    d.status = $('#status').val();
                    d.akses = $('#akses').val();
                    d.tte = $('#tte').val();
                    @if(auth()->check())
                    d.kode_desa = $('#filter-kode-desa').val();
                    d.domain = $('#filter-domain').val();
                    d.jumlah_domain = $('#filter-jumlah-domain').val();
                    d.punya_riwayat = $('#filter-punya-riwayat').val();
                    @endif
                }
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
                    data: 'versi',
                    searchable: false
                },
                {
                    data: 'modul_tte',
                    render: function (data, type, row) {
                        if (data == 1) {
                            return '<span class="badge badge-success">Aktif</span>';
                        } else {
                            return '<span class="badge badge-danger">Tidak Aktif</span>';
                        }
                    },
                    searchable: false
                },
                {
                    data: 'jenis_server',
                    name: 'jenis_server',
                    render: function (data, type, row) {
                        
                        const versiHost = row.versi_hosting;
                        const versiLokal = row.versi_lokal;

                        let jenis = 'Tidak Diketahui';

                        if (!versiHost && versiLokal) {
                            jenis = (versiLokal.includes('-premium')) ? 'Premium Terbaru' : 'Offline';
                        } else if (versiHost && !versiLokal) {
                            jenis = (versiHost.includes('-premium')) ? 'Premium Terbaru' : 'Online';
                        } else if (versiHost && versiLokal) {
                            jenis = (versiHost.includes('-premium')) ? 'Premium Terbaru' : 'Online';
                        }

                        if (jenis === 'Online') {
                            return '<span class="badge badge-success">Online</span>';
                        } else if (jenis === 'Offline') {
                            return '<span class="badge badge-danger">Offline</span>';
                        } else if (jenis === 'Premium Terbaru') {
                            return '<span class="badge badge-warning">Premium</span>';
                        } else {
                            return '<span class="badge badge-light">Tidak Diketahui</span>';
                        }
                    },
                    searchable: false
                },
                @if(auth()->check())
                {
                    data: 'domain',
                    name: 'domain',
                    render: function (data, type, row) {
                        if (! data) {
                            return '<span class="text-muted">-</span>';
                        }

                        var jumlah = row.jumlah_domain || 0;

                        return '<code>'+ $('<div>').text(data).html() +'</code>'
                            + (jumlah > 1 ? ' <span class="badge badge-warning">'+ jumlah +' domain</span>' : '');
                    }
                },
                {
                    data: 'kode_desa',
                    name: 'kode_desa',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<a href="'+ '{{ url('desa/domain-history') }}' +'/'+ $('<div>').text(row.id).html() +'" class="btn btn-sm btn-primary" title="Riwayat Domain"><i class="fas fa-history"></i></a>';
                    }
                }
                @endif

            ],
            order: [
                [1, 'desc']
            ],
        })        

        $(document).ready(function () {
            $('#filter-form').click(function () {
                semuaDesa.ajax.reload();
            })
            $('#reset-form').click(function () {
                $('#collapse-filter-form select').val('')

                $('#kode_provinsi').val('').trigger('change');
                $('#kode_kabupaten').val('').trigger('change');
                $('#kode_kecamatan').val('').trigger('change');
                $('#status').val('').trigger('change');
                $('#akses').val('').trigger('change');
                $('#tte').val('').trigger('change');
                @if(auth()->check())
                $('#filter-kode-desa').val('');
                $('#filter-domain').val('');
                $('#filter-jumlah-domain').val('');
                $('#filter-punya-riwayat').val('');
                @endif

                semuaDesa.ajax.reload();
            })
            @if(auth()->check())
            $('#filter-kode-desa, #filter-domain').on('keyup change', function () {
                semuaDesa.ajax.reload();
            })
            $('#filter-jumlah-domain, #filter-punya-riwayat').on('change', function () {
                semuaDesa.ajax.reload();
            })
            @endif
            $('#btn-export').click(function(){
                const _href = $(this).data('href')
                window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#table-semua-desa').DataTable().ajax.params())
            })   
        })
        

    </script>
@endpush