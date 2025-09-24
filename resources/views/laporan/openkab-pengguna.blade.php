@extends('adminlte::page')

@section('title', 'Pengguna OpenKab Aktif')

@section('content_header')
<h1>Pengguna OpenKab Aktif</h1>
@stop

@section('content')
<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengguna OpenKab</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="table-openkab-pengguna">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kabupaten</th>
                    <th>Akses Admin Selama 30 Hari</th>
                    <th>Jumlah Pengguna Terdaftar</th>
                    <th>Login Terakhir</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#table-openkab-pengguna').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('laporan.openkab.pengguna') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    width: '5%'
                },
                {
                    data: 'nama_kab',
                    name: 'nama_kab'
                },
                {
                    data: 'status_akses_display',
                    name: 'status_akses',
                    orderable: false
                },
                {
                    data: 'jumlah_pengguna_terdaftar',
                    name: 'jumlah_pengguna_terdaftar',
                    className: 'text-right',
                    searchable: false
                },
                {
                    data: 'login_terakhir',
                    name: 'login_terakhir',
                    searchable: false
                }
            ],
            order: [[1, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@stop