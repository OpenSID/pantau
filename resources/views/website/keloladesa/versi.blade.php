<div class="card mt-3">
    <div class="card-body" id="block_table_versi">
        <div class="table-responsive">
            <table class="table" id="table-versi">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Versi</th>
                        <th>Jumlah</th>                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="text-center"><a href="{{ url('web/keloladesa/versi') }}" target="_blank" rel="noopener noreferrer">Lihat Semua Versi</a></div>
        </div>
    </div>
</div>

@push('js')
    <script>
        var desaVersi = $('#table-versi').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            pageLength: 5,
            dom: 't',
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[1, 'desc']],            
            ajax: {
                url: `{{ url('web/keloladesa/versi') }}`,
                method: 'get',
                data: function(data) {
                    data.kode_provinsi = $('select[name=provinsi]').val()
                    data.kode_kabupaten = $('select[name=kabupaten]').val()
                    data.kode_kecamatan = $('select[name=kecamatan]').val()
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'versi',
                    name: 'versi',                    
                },                
                {
                    name: 'jumlah',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('web/keloladesa/versi/detail') }}?versi=${data.versi}"><span class="badge badge-success">${data.jumlah}</span></a>`
                    },
                    searchable: false,
                    orderable: false
                },
            ]
        })

        $('#block_table_versi').change(function() {
            desaVersi.ajax.reload();
        })
    </script>
@endpush