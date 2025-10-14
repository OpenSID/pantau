<div class="card mt-3">
    <div class="card-body" id="block_table_opendk_baru">
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
                // add data parameter for filtering
                data: function(d) {
                    d.kode_provinsi = $('select[name=provinsi]').val()
                    d.kode_kabupaten = $('select[name=kabupaten]').val()
                    d.kode_kecamatan = $('select[name=kecamatan]').val()
                    d.period = $('input[name=periods]').val() || '';
                },                
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
        
           $('#block_table_opendk_baru').change(function() {
                desaBaru.ajax.reload();
            })                                            
    </script>
@endpush