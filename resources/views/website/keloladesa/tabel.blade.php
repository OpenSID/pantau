<div class="card mt-3">
    <div class="card-body" id="block_table_keloladesa_baru">
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


        var desaBaru = $('#table-desa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('web/keloladesa/install_baru') }}`,
                method: 'get',
                data: function(data) {
                    data.kode_provinsi = $('select[name=provinsi]').val()
                    data.kode_kabupaten = $('select[name=kabupaten]').val()
                    data.kode_kecamatan = $('select[name=kecamatan]').val()
                },
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'id_device'
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
        });


        $(document).ready(function() {

            $('#block_table_keloladesa_baru').change(function() {
                desaBaru.ajax.reload();
            });


        })
    </script>
@endpush