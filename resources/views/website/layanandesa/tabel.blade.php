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
        var desaBaru = $('#table-desa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('web/layanandesa/install_baru') }}`,
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
    </script>
@endpush