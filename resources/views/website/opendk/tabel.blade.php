<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-desa-baru">
                <thead>
                    <tr>
                        <th>No</th>                        
                        <th>Tgl Terpantau</th>                        
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Provinsi</th>                        
                        <th>Versi</th>                        
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
            language: { url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json" }
        });

        var desaBaru = $('#table-desa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('web/opendk/install_baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },                
                {
                    data: 'created_at',
                    searchable: false
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
            ],
            order: [
                [1, 'desc']
            ],
        })        
    </script>
@endpush