@extends('layouts.index')

@section('title', 'Tambah Data Kabupaten')

@section('content_header')
<h1>Kabupaten<small class="font-weight-light ml-1 text-md">Tambah Data Kabupaten</small></h1>
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
                        <a href="{{ route('kabupaten.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Kabupaten
                        </a>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('kabupaten.store') }}">
                <div class="card-body">
                    @method('POST')
                    @csrf

                    <div class="row">
                        <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                        <div class="col-4">
                            <input id="kode_provinsi" name="parent_code" class="form-control" placeholder="00"
                                type="text" readonly required />
                        </div>
                        <div class="col-8">
                            <select class="form-control" id="list_provinsi" data-placeholder="Pilih Provinsi"
                                style="width: 100%;" required>
                                <option selected>Pilih Provinsi</option>
                            </select>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <label class="control-label col-sm-12">Kabupaten <span class="required">*</span></label>
                        <div class="col-4">
                            <input name="region_code" id="region_code" class="form-control" placeholder="00.00"
                                data-inputmask="{!! "'mask' :'99.99'" !!}" data-mask required disabled />
                        </div>
                        <div class="col-8">
                            <input name="region_name" id="region_name" class="form-control" placeholder="Nama Kabupaten"
                                maxlength="80" required disabled />
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-sm btn-danger btn-block"><i class="fas fa-times"></i>
                                &ensp;Batal</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-sm btn-success btn-block float-right"><i class="fas fa-save"></i>
                                &ensp;Simpan</button>
                        </div>
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

            $('#list_provinsi').change(function() {
                $("#kode_provinsi").val($('#list_provinsi').val());

                $("#region_code").attr('disabled', false);
                $("#region_name").attr('disabled', false);
                $("#region_code").val($('#list_provinsi').val());
                $("#region_name").val('');
            });
        })
</script>
@endpush