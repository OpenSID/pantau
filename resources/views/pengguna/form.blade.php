@extends('layouts.index')

@section('title', 'Tambah Pengguna')

@section('content_header')
    <h1>Pengguna<small class="font-weight-light ml-1 text-md">Tambah Pengguna</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-6">
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
                <div class="card-header">
                    <div class="float-left">
                        <div class="btn-group">
                            <a href="{{ route('akun-pengguna.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('akun-pengguna.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Group <span class="text-danger">*</span></label>
                            <select name="id_grup" class="form-control">
                                <option value="1">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Akses Wilayah (Provinsi) <span class="text-danger">*</span></label>
                            <select id="provinsi-akses" name="provinsi_akses" required class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label>Akses Wilayah (Kabupaten) <span class="text-danger">*</span></label>
                            <select id="kabupaten-akses" name="kabupaten_akses" class="form-control" disabled required></select>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-danger">&ensp;Batal</button>
                            <button class="btn btn-success float-right">&ensp;Simpan</button>
                        </div>
                    @push('js')
                    <script>
                        $('#provinsi-akses').select2({
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

                        $('#provinsi-akses').on('change', function(e) {
                            let provinsiIds = $(this).val();
                            if (provinsiIds && provinsiIds.length > 0) {
                                $('#kabupaten-akses').prop('disabled', false);
                                $('#kabupaten-akses').val(null).trigger('change');
                                $('#kabupaten-akses').select2({
                                    ajax: {
                                        headers: {
                                            "Authorization": `Bearer {{ config('tracksid.sandi.sanctum_token') }}`,
                                        },
                                        url: "{{ url('api/v1/wilayah/kabupaten') }}",
                                        dataType: 'json',
                                        delay: 400,
                                        data: function(params) {
                                            return {
                                                search: params.term,
                                                kode_prov: provinsiIds, // array of selected provinsi
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
                            } else {
                                $('#kabupaten-akses').prop('disabled', true);
                                $('#kabupaten-akses').val(null).trigger('change');
                            }
                        });
                    </script>
                    @endpush
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
