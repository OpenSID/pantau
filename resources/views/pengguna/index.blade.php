@extends('layouts.index')

@section('title', 'Data Pengguna')

@section('content_header')
    <h1>Data Pengguna</h1>
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
                            <a href="{{route('akun-pengguna.create')}}" class="btn btn-primary float-right mb-3">Tambah Pengguna</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Group</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MODAL EDIT --}}
            <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edit-modal-label">Ubah: </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                @method('PUT')
                                @csrf
                                <div class="form-group">
                                    <label>Group <span class="text-danger">*</span></label>
                                    <select name="id_grup" class="form-control">
                                        <option value="1">Administrator</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" id="username" class="form-control" name="username">
                                </div>
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control" name="name">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" id="email" class="form-control" name="email">
                                </div>

                                <a type="button" class="btn btn-warning float-right ml-2" data-dismiss="modal">Batal</a>
                                <button class="btn btn-success float-right">Perbarui</button>
                            </form>
                        </div>
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
                        <div class="modal-body">Apakah anda yakin ingin menghapus pengguna ini?</div>
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
                ajax: '{{route('get.akun-pengguna')}}',
                columns: [
                    { data: 'username', name: 'username' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'id_grup', name: 'id_grup' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        $('#edit-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var submit = button.data('submit')
            var username = button.data('username')
            var name = button.data('name')
            var email = button.data('email')
            var id_grup = button.data('id_grup')

            var modal = $(this)
            modal.find('.modal-title').text('Ubah: ' + name)
            modal.find('.modal-body form').attr('action', submit)
            modal.find('.modal-body #username').val(username)
            modal.find('.modal-body #name').val(name)
            modal.find('.modal-body #email').val(email)
            modal.find('.modal-body #id_grup option[value='+id_grup+']').attr('selected','selected')
        });

        $('#delete-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var name = button.data('name')
            var submit = button.data('submit')

            var modal = $(this)
            modal.find('.modal-title').text('Hapus: ' + name)
            modal.find('.modal-footer form').attr('action', submit)
        });
    </script>
@endsection
