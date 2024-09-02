<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kabupaten yang belum ada desa OpenSID</h3>                
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="table-kabupaten-kosong">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Kabupaten</th>
                                <th>Nama Kabupaten</th>
                                <th>Nama Provinsi</th>
                                <th>Jumlah Desa</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        $('#table-kabupaten-kosong').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('datatables:kabupaten-kosong') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'region_code'
                },
                {
                    data: 'nama_kabupaten'
                },
                {
                    data: 'nama_provinsi'
                },
                {
                    data: 'jml_desa'
                },
            ]
        })
    })
</script>
@endpush
