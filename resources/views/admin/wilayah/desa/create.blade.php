@extends('layouts.index')

@section('title', 'Tambah Data Desa')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Tambah Data Desa</small></h1>
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
                            <a href="{{ url('desa') }}" class="btn btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Desa
                            </a>
                        </div>
                    </div>
                </div>
                <form method="post" action="{{ url('desa/store') }}">
                    <div class="card-body">
                        @csrf

                        <div class="row">
                            <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="provinsi_id" class="form-control" placeholder="00" type="text" readonly
                                    value="" />
                                <input id="nama_provinsi" type="hidden" name="nama_provinsi" value="" />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_provinsi" name="provinsi_id" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kabupaten <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kabupaten_id" class="form-control" placeholder="00.00" type="text" readonly
                                    value="" />
                                <input id="nama_kabupaten" type="hidden" name="nama_kabupaten" value="" />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_kabupaten" name="kabupaten_id" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Kabupaten</option>
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kecamatan <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kecamatan_id" class="form-control" placeholder="00.00.00" type="text" readonly
                                    value="" />
                                <input id="nama_kecamatan" type="hidden" name="nama_kecamatan" value="" />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_kecamatan" name="kecamatan_id"
                                    data-placeholder="Pilih kecamatan" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Desa <span class="required">*</span></label>
                            <div class="col-4">
                                <input name="kode_desa" class="form-control" required />
                            </div>
                            <div class="col-8">
                                <input name="nama_desa" class="form-control" required />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <button type="reset" class="btn btn-danger btn-block"><i class="fas fa-times"></i>
                                    Batal</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success btn-block float-right"><i class="fas fa-save"></i>
                                    Simpan</button>
                            </div>
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

            list_kabupaten();

            $('#list_provinsi').change(function() {
                $("#provinsi_id").val($('#list_provinsi').val());
                $("#nama_provinsi").val($('#list_provinsi option:selected').text());

                list_kabupaten();
            });

            list_kecamatan();

            $('#list_kabupaten').change(function() {
                $("#kabupaten_id").val($('#list_kabupaten').val());
                $("#nama_kabupaten").val($('#list_kabupaten option:selected').text());

                list_kecamatan();
            });

            $('#list_kecamatan').change(function() {
                $("#kecamatan_id").val($('#list_kecamatan').val());
                $("#nama_kecamatan").val($('#list_kecamatan option:selected').text());
            });

            function list_kabupaten() {
                $('#list_kabupaten').select2({
                    ajax: {
                        url: host + '?token=' + token + '&kode=' + $('#list_provinsi')
                            .val(),
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
                                        id: item.kode_kab,
                                        text: item.nama_kab,
                                    }
                                }),
                                pagination: response.pagination
                            };
                        },
                        cache: true
                    }
                });
            }

            function list_kecamatan() {
                $('#list_kecamatan').select2({
                    ajax: {
                        url: host + '?token=' + token + '&kode=' + $('#list_kabupaten')
                            .val(),
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
                                        id: item.kode_kec,
                                        text: item.nama_kec
                                    }
                                }),
                                pagination: response.pagination
                            };
                        },
                        cache: true
                    }
                });
            }
        })
    </script>
@endpush
