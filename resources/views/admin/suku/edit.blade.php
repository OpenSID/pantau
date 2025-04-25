@extends('layouts.index')

@section('title', 'Ubah Data Suku')

@section('content_header')
    <h1>Suku<small class="font-weight-light ml-1 text-md">Ubah Data Suku</small></h1>
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
                            <a href="{{ route('suku.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Suku
                            </a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('suku.update', $suku->id) }}">
                    <div class="card-body">
                        @method('PUT')
                        @csrf

                        <div class="form-group">
                            <label class="control-label col-12">Nama <span class="required">*</span></label>
                            <div class="col-12">
                                <input class="form-control" value="{{ $suku->name }}" type="text" name="name"
                                    id="name" placeholder="Nama Suku" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-12">Provinsi <span class="required">*</span></label>
                            <div class="col-12">
                                <select class="form-control" name="tbl_region_id" id="list_provinsi"
                                    data-placeholder="Pilih Provinsi" style="width: 100%;" required>
                                    <option selected>Pilih Provinsi</option>
                                    <option value="{{ $suku->region->region_code }}" selected>
                                        {{ $suku->region->region_name }}</option>
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
    <script src="{{ asset('/vendor/inputmask/jquery.inputmask.js') }}"></script>
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

                // Kosongkan pilihan kab, kec, dan isian suku
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

                // Kosongkan pilihan kec, dan isian suku
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
                var getkab = $('#list_kabupaten').val();
                var getkodekab = '{!! $suku->kode_kabupaten !!}';
                var getnamakab = '{!! $suku->nama_kabupaten !!}';
                if (getkab == getnamakab) {
                    var getkab = getkodekab;
                }

                $('#list_kecamatan').select2({
                    ajax: {
                        url: host + '?token=' + token + '&kode=' + getkab,
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

            $('#region_code').keyup(function() {
                var akas = this.value.slice(9, 11);
                if (akas == '99') {
                    $('#keterangan').addClass('required');
                    $('.suku_persiapan').show();
                } else {
                    $('#keterangan').val('');
                    $('#keterangan').removeClass('required');
                    $('.suku_persiapan').hide();
                }
            });
        })
    </script>
@endpush
