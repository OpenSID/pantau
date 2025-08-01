<div class="text-bold" id="div-title-install-baru">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <b>
            Daftar Pengguna Baru LayananDesa 7 Hari Terakhir
        </b>
        <a href="{{ route('web.layanandesa.detail') }}" class="btn btn-sm btn-primary">Data Selengkapnya</a>
    </div>
</div>
<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-desa-baru">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Id Perangkat</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Versi Mobile</th>
                        <th>Akses Terakhir</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script>
        $.extend($.fn.dataTable.defaults, {
            language: {
                url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json"
            }
        });
        $(document).ready(function() {
            var desaBaru = $('#table-desa-baru').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                ajax: {
                    url: `{{ url('web/layanandesa/install_baru') }}`,
                    data: function(d) {
                        d.kode_provinsi = $('#provinsi').val()
                        d.kode_kabupaten = $('#kabupaten').val()
                        d.kode_kecamatan = $('#kecamatan').val()
                        d.status = null
                        d.period = $('input[name=periods]').val()
                    },
                    method: 'get',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'id'
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
                        data: 'versi',
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        searchable: false
                    },
                ],
                order: [
                    [1, 'desc']
                ],
            })

            $('#filter').click(function() {
                desaBaru.draw();
            });
            $('input[name=periods]').change(function() {
                const _period = $(this).val().replace('  -  ', 'sd');
                $('#div-title-install-baru').text('Daftar Pengguna Baru LayananDesa Periode ' + _period);
                desaBaru.draw();
            })
        })
    </script>
@endpush
