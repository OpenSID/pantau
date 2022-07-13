@extends('layouts.index')

@section('title', 'Ubah Data Desa')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Ubah Data Desa</small></h1>
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
                            <a href="{{ url('desa') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Desa
                            </a>
                        </div>
                    </div>
                </div>
                <form method="post" action="{{ url('desa/' . $desa->id) }}">
                    <div class="card-body">
                        @method('PUT')
                        @csrf

                        <div class="row">
                            <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kode_provinsi" class="form-control" placeholder="00" type="text"
                                    value="{{ $desa->kode_provinsi }}" readonly />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_provinsi" data-placeholder="Pilih Provinsi"
                                    style="width: 100%;" required>
                                    <option selected>Pilih Provinsi</option>
                                    @if ($desa->kode_provinsi || $desa->nama_provinsi)
                                        <option selected>{{ $desa->nama_provinsi }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kabupaten <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kode_kabupaten" class="form-control" placeholder="00.00" type="text"
                                    value="{{ $desa->kode_kabupaten }}" readonly />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_kabupaten" data-placeholder="Pilih Kabupaten"
                                    style="width: 100%;" @if (!$desa->nama_kabupaten) 'disabled' @endif required>
                                    <option selected>Pilih Kabupaten</option>
                                    @if ($desa->kode_kabupaten || $desa->nama_kabupaten)
                                        <option selected>{{ $desa->nama_kabupaten }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kecamatan <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kode_kecamatan" name="parent_code" class="form-control" placeholder="00.00.00"
                                    type="text" value="{{ $desa->kode_kecamatan }}" readonly />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_kecamatan" data-placeholder="Pilih Kecamatan"
                                    style="width: 100%;" @if (!$desa->nama_kecamatan) 'disabled' @endif required>
                                    <option selected>Pilih Kecamatan</option>
                                    @if ($desa->kode_kecamatan || $desa->nama_kecamatan)
                                        <option selected>{{ $desa->nama_kecamatan }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Desa <span class="required">*</span></label>
                            <div class="col-4">
                                <input name="region_code" id="region_code" class="form-control" placeholder="00.00.00.0000"
                                    data-inputmask="{!! "'mask':'" . $desa->kode_kecamatan . ".9999'" !!}" data-mask value="{{ $desa->kode_desa }}"
                                    @if (!$desa->kode_desa) 'disabled' @endif required />
                            </div>
                            <div class="col-8">
                                <input name="region_name" id="region_name" class="form-control" placeholder="Nama Desa"
                                    maxlength="80" value="{{ $desa->nama_desa }}"
                                    @if (!$desa->nama_desa) 'disabled' @endif required />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <button type="reset" class="btn btn-sm btn-danger btn-block"><i class="fas fa-times"></i>
                                    Batal</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-sm btn-success btn-block float-right"><i class="fas fa-save"></i>
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

            $('[data-mask]').inputmask();

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
                $("#kode_provinsi").val($('#list_provinsi').val());

                // Kosongkan pilihan kab, kec, dan isian desa
                $("#list_kabupaten").attr('disabled', false);
                $("#list_kabupaten").val('');
                $("#kode_kabupaten").val('');

                $("#list_kecamatan").attr('disabled', true);
                $("#list_kecamatan").val('');
                $("#kode_kecamatan").val('');

                $("#region_code").attr('disabled', true);
                $("#region_name").attr('disabled', true);
                $("#region_code").val('');
                $("#region_name").val('');

                list_kabupaten();
            });

            list_kecamatan();
            $('#list_kabupaten').change(function() {
                $("#kode_kabupaten").val($('#list_kabupaten').val());

                // Kosongkan pilihan kec, dan isian desa
                $("#list_kecamatan").attr('disabled', false);
                $("#list_kecamatan").val('');
                $("#kode_kecamatan").val('');

                $("#region_code").attr('disabled', true);
                $("#region_name").attr('disabled', true);
                $("#region_code").val('');
                $("#region_name").val('');

                list_kecamatan();
            });

            $('#list_kecamatan').change(function() {
                $("#kode_kecamatan").val($('#list_kecamatan').val());
                $("#region_code").attr('data-inputmask', "'mask':'" + $('#list_kecamatan').val() +
                    ".9999'");
                $("#region_code").attr('disabled', false);
                $("#region_name").attr('disabled', false);
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
