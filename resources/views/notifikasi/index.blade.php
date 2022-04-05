@extends('layouts.index')

@section('title', 'Notifikasi')

@section('content_header')
    <h1>Notifikasi</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if(session('alert'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{session('alert')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(session('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{session('danger')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('notifikasi.create')}}" class="btn btn-primary float-right mb-3">Tambah Notifikasi</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                            <tr>
                                <th>...</th>
                                <th>Frekuensi</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Jenis</th>
                                <th>Server</th>
                                <th>Isi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MODAL DELETE --}}
            <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="delete-modal-label">Hapus: </h4>
                        </div>
                        <div class="modal-body">Apakah anda yakin ingin menghapus pesan ini?</div>
                        <div class="modal-footer">
                            <form method="POST">
                                @csrf
                                @method('DELETE')
                                <button  class="btn btn-danger">Ya</button>
                            </form>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Tidak, batalkan</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{url('notifikasi/show')}}',
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'frekuensi', name: 'frekuensi' },
                    { data: 'kode', name: 'kode' },
                    { data: 'judul', name: 'judul' },
                    { data: 'jenis', name: 'jenis' },
                    { data: 'server', name: 'server' },
                    { data: 'isi', name: 'isi' },
                ]
            });
        });

        $('#delete-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var judul = button.data('judul')
            var submit = button.data('submit')

            var modal = $(this)
            modal.find('.modal-title').text('Hapus: ' + judul)
            modal.find('.modal-footer form').attr('action', submit)
        });
    </script>
@endsection
