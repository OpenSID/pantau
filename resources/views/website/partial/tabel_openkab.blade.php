<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-openkab">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kabupaten</th>
                        <th>Nama Kabupaten</th>
                        <th>Nama Provinsi</th>
                        <th>Jumlah Desa</th>
                        <th>Versi Terpasang</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
    $.extend($.fn.dataTable.defaults, {
        language: { url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json" }
    });
    var kabupaten = $('#table-openkab').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('web/openkab') }}`,
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
                {
                    data: 'nama_prov',
                    name: 'nama_prov'
                },
                {
                    data: 'jumlah_desa',
                    name: 'jumlah_desa'
                },
                {
                    data: 'versi',
                    name: 'versi'
                },
            ]
        })
</script>
@endpush