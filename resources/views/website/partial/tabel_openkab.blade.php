<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table-kerja-sama">
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
                    <tr>
                        <td>1</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>11.12</td>
                        <td>Kabupaten</td>
                        <td>Provinsi</td>
                        <td>100</td>
                        <td>2407.0.0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('js')
<script>
    var kecamatan = $('#table-kerja-sama').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('openkab/kerja-sama') }}`,
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
            ]
        })
</script>
@endsection