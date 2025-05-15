@extends('layouts.index')

@section('title', 'Tambah Data Marga')

@section('content_header')
    <h1>Marga<small class="font-weight-light ml-1 text-md">Tambah Data Marga</small></h1>
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
                            <a href="{{ route('marga.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Marga
                            </a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('marga.store') }}">
                    <div class="card-body">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-12">Nama <span class="required">*</span></label>
                            <div class="col-12">
                                <input class="form-control" type="text" name="name" id="name"
                                    placeholder="Nama Marga" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-12">Provinsi <span class="required">*</span></label>
                            <div class="col-12">
                                <select class="form-control" name="tbl_region_id" id="list_provinsi"
                                    data-placeholder="Pilih Provinsi" style="width: 100%;" required>
                                    <option selected>Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-12">Suku <span class="required">*</span></label>
                            <div class="col-12">
                                <select class="form-control" name="ethnic_group_id" id="list_suku"
                                    data-placeholder="Pilih Suku" style="width: 100%;" required>
                                    <option selected>Pilih Suku</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="reset" class="btn btn-sm btn-danger"><i class="fas fa-times"></i>
                                &ensp;Batal</button>
                            <button class="btn btn-sm btn-success float-right"><i class="fas fa-save"></i>
                                &ensp;Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            const host = "{{ url('api/wilayah/list_wilayah/') }}";
            const urlSuku = "{{ url('api/wilayah/suku/') }}";
            const token = "{{ config('tracksid.sandi.dev_token') }}";

            $('#list_provinsi').select2({
                ajax: {
                    url: host + '?token=' + token,
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            cari: params.term,
                            page: params.page || 1,
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(response.results, function(item) {
                                return {
                                    id: item.kode_prov,
                                    text: item.nama_prov,
                                }
                            }),
                            pagination: response.pagination
                        };
                    },
                    cache: true
                }
            });

            $('#list_suku').select2({
                ajax: {
                    url: urlSuku + '?token=' + token,
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            cari: params.term,
                            page: params.page || 1,
                            kode_prov: $('#list_provinsi').val(),
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(response.results, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                }
                            }),
                            pagination: response.pagination
                        };
                    },
                    cache: true
                }
            });

            $('#list_provinsi').on('change', function() {
                $('#list_suku').val(null).trigger('change');
            });
        })
    </script>
@endpush
