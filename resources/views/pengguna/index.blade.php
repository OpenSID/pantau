@extends('layouts.index')

@section('title', 'Daftar Pengguna')

@section('content_header')
    <h1>Pengguna<small class="font-weight-light ml-1 text-md">Daftar Pengguna</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if (session('alert'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('alert') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <a href="{{ route('akun-pengguna.create') }}" class="btn btn-primary btn-sm mb-3">Tambah Data</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="10%">Action</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MODAL EDIT --}}
            <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label"
                aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role_id" class="form-control" id="edit-role-id" onchange="updateWilayahRequired()" required>
                                        @foreach ($roles as $id => $nama)
                                            <option value="{{ $id }}">{{ $nama }}</option>
                                        @endforeach
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
                                <div class="form-group">
                                    <label>Akses Wilayah (Provinsi) <span class="text-danger">*</span></label>
                                    <select id="edit-provinsi-akses" name="provinsi_akses" required class="form-control"></select>
                                </div>
                                <div class="form-group">
                                    <label>Akses Wilayah (Kabupaten) <span class="text-danger">*</span></label>
                                    <select id="edit-kabupaten-akses" name="kabupaten_akses" class="form-control" disabled required></select>
                                </div>

                                <a type="button" class="btn btn-danger float-right ml-2" data-dismiss="modal">Batal</a>
                                <button class="btn btn-success float-right">Perbarui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL DELETE --}}
            <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label"
                aria-hidden="true">
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
                                <button class="btn btn-danger">Ya</button>
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
            ajax: '{{ route('akun-pengguna.datatables') }}',
            columns: [
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'role',
                    name: 'role'
                },
            ]
        });
    });

    // Dinamis: jika role Admin Wilayah dipilih, provinsi/kabupaten wajib, selainnya tidak wajib
        var adminWilayahId = null;
        @foreach ($roles as $id => $nama)
            @if (strtolower($nama) === 'admin wilayah')
                adminWilayahId = '{{ $id }}';
            @endif
        @endforeach

        window.updateWilayahRequired = function() {
            var selected = $('#edit-role-id').val();
            if (selected == adminWilayahId) {
                $('#edit-provinsi-akses').attr('required', true);
                $('#edit-kabupaten-akses').attr('required', true);
                $('#edit-provinsi-akses').closest('div.form-group').show();
                $('#edit-kabupaten-akses').closest('div.form-group').show();
            } else {
                $('#edit-provinsi-akses').removeAttr('required');
                $('#edit-kabupaten-akses').removeAttr('required');
                $('#edit-provinsi-akses').closest('div.form-group').hide();
                $('#edit-kabupaten-akses').closest('div.form-group').hide();
            }
        }
    $('#edit-modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var submit = button.data('submit')
        var username = button.data('username')
        var name = button.data('name')
        var email = button.data('email')
        var role_id = button.data('role_id')
        var kabupaten = button.data('kode_kabupaten')
        var provinsi = button.data('kode_provinsi')
        var modal = $(this)
        modal.find('.modal-title').text('Ubah: ' + name)
        modal.find('.modal-body form').attr('action', submit)
        modal.find('.modal-body #username').val(username)
        modal.find('.modal-body #name').val(name)
        modal.find('.modal-body #email').val(email)
        modal.find('.modal-body #edit-role-id').val(role_id);
        modal.find('.modal-body #edit-role-id').trigger('change');

        // Set provinsi
        if (provinsi) {
            let provinsiOption = new Option(provinsi.text, provinsi.id, true, true);
            $('#edit-provinsi-akses').append(provinsiOption).trigger('change');
            $('#edit-provinsi-akses').trigger('change');
            // Enable kabupaten select
            $('#edit-kabupaten-akses').prop('disabled', false);
            if( kabupaten ) {
                let kabupatenOption = new Option(kabupaten.text, kabupaten.id, true, true);
                $('#edit-kabupaten-akses').append(kabupatenOption).trigger('change');
                $('#edit-kabupaten-akses').trigger('change');
            } else {
                $('#edit-kabupaten-akses').val(null).trigger('change');
            }
        } else {
            $('#edit-provinsi-akses').val(null).trigger('change');
            $('#edit-kabupaten-akses').prop('disabled', true);
            $('#edit-kabupaten-akses').val(null).trigger('change');
        }
    });

    $('#delete-modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var name = button.data('name')
        var submit = button.data('submit')

        var modal = $(this)
        modal.find('.modal-title').text('Hapus: ' + name)
        modal.find('.modal-footer form').attr('action', submit)
    });

    // Inisialisasi Select2 wilayah pada modal edit (hanya sekali)
    $('#edit-provinsi-akses').select2({
        dropdownParent: $('#edit-modal'),
        width: '100%',
        ajax: {
            headers: {
                "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
            },
            url: "{{ url('api/v1/wilayah/provinsi') }}",
            dataType: 'json',
            delay: 400,
            data: function(params) {
                return {
                    search: params.term,
                    page: params.page
                };
            },
            processResults: function(response, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(response.data, function(item) {
                        return {
                            id: item.kode_prov,
                            text: item.nama_prov,
                        }
                    }),
                    pagination: {
                        more: params.page < response.last_page
                    }
                };
            }
        }
    });
    $('#edit-kabupaten-akses').select2({
        dropdownParent: $('#edit-modal'),
        width: '100%',
        ajax: {
            headers: {
                "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
            },
            url: "{{ url('api/v1/wilayah/kabupaten') }}",
            dataType: 'json',
            delay: 400,
            data: function(params) {
                // Ambil provinsi terpilih
                let provinsiIds = $('#edit-provinsi-akses').val();
                return {
                    search: params.term,
                    kode_prov: provinsiIds,
                    page: params.page
                };
            },
            processResults: function(response, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(response.data, function(item) {
                        return {
                            id: item.kode_kab,
                            text: item.nama_kab,
                        }
                    }),
                    pagination: {
                        more: params.page < response.last_page
                    }
                };
            }
        }
    });


    // Prevent bubbling on select2 select event (provinsi/kabupaten)
    $('#edit-kabupaten-akses, #edit-provinsi-akses').on('select2:select', function(e) {
        e.stopPropagation();
    });
    // disable modal closing when clicking on select2 results
    $('#edit-modal').on('click', '.select2-results', function(e) {
        e.stopPropagation();
    });
    // Enable/disable kabupaten select
    $('#edit-provinsi-akses').on('change', function(e) {
        let provinsiIds = $(this).val();
        if (provinsiIds && provinsiIds.length > 0) {
            $('#edit-kabupaten-akses').prop('disabled', false);
            $('#edit-kabupaten-akses').val(null).trigger('change');
        } else {
            $('#edit-kabupaten-akses').prop('disabled', true);
            $('#edit-kabupaten-akses').val(null).trigger('change');
        }
    });
</script>
@endsection
