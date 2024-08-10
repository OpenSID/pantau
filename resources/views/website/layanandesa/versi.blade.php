<div class="card mt-3">
    <div class="card-body">
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
            <div class="text-center"><a href="{{ url('web/layanandesa/versi') }}" target="_blank" rel="noopener noreferrer">Lihat Semua Versi</a></div>
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
                url: `{{ url('web/layanandesa/versi') }}`,
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
                    name: 'versi',                    
                },                
                {
                    name: 'jumlah',
                    data: function (data) {
                        return `<a target="_blank" href="{{ url('web/layanandesa/versi/detail') }}?versi=${data.versi}"><span class="badge badge-success">${data.jumlah}</span></a>`
                    },
                    searchable: false,
                    orderable: false
                },                
            ]
        })        
    </script>
@endpush