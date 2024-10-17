<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-desa-baru">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Terpantau</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Web</th>
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
                url: `{{ route('datatables:desa-baru') }}`,
                method: 'get',
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
                    data: function (data) {
                        if (data.url_hosting) {
                            return `<a target="_blank" href="https://${data.url_hosting}">https://${data.url_hosting}</a>`
                        } else if (data.url_lokal) {
                            return `<a target="_blank" href="http://${data.url_lokal}">http://${data.url_lokal}</a>`
                        }

                        return '';
                    },
                    searchable: false,
                    orderable: false,
                    visible : {{ auth()->check() == false ?'false' : 'true' }}
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