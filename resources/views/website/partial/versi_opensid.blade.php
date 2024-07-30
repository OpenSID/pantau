<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-versi">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Versi</th>
                        <th>Online</th>
                        <th>Offline</th>                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="text-center"><a href="{{ url('laporan/versi') }}" target="_blank" rel="noopener noreferrer">Lihat Semua Versi</a></div>
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
                url: `{{ url('laporan/versi') }}`,
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
                    data: 'versi',
                    name: 'x.versi',                    
                },                
                {
                    name: 'online',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?versi_hosting=${data.versi}"><span class="badge badge-success">${data.online}</span></a>`
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    name: 'offline',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('laporan/desa') }}?versi_lokal=${data.versi}"><span class="badge badge-danger">${data.offline}</span></a>`
                    },
                    searchable: false,
                    orderable: false
                },
            ]
        })        
    </script>
@endpush
