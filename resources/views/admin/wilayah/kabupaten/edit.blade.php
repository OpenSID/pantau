@extends('layouts.index')

@section('title', 'Ubah Data Kabupaten')

@section('content_header')
<h1>Kabupaten<small class="font-weight-light ml-1 text-md">Ubah Data Kabupaten</small></h1>
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
            <form method="POST" action="{{ route('kabupaten.update', $kabupaten->id) }}">
                <div class="card-body">
                    @method('PUT')
                    @csrf

                    <div class="row">
                        <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                        <div class="col-4">
                            <input id="kode_provinsi" name="parent_code" class="form-control" placeholder="00"
                                type="text" value="{{ $kabupaten->kode_provinsi }}" readonly required />
                        </div>
                        <div class="col-8">
                            <select class="form-control" id="list_provinsi" data-placeholder="Pilih Provinsi"
                                style="width: 100%;" required>
                                <option selected>Pilih Provinsi</option>
                                @if ($kabupaten->kode_provinsi || $kabupaten->nama_provinsi)
                                <option selected>{{ $kabupaten->nama_provinsi }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <label class="control-label col-sm-12">Kabupaten <span class="required">*</span></label>
                        <div class="col-4">
                            <input name="region_code" id="region_code" class="form-control" placeholder="00.00"
                                data-inputmask="{!! "'mask' :'" . $kabupaten->kode_provinsi . ".99'" !!}" data-mask
                            value="{{ $kabupaten->kode_kabupaten }}"
                            @if (!$kabupaten->kode_kabupaten) 'disabled' @endif required />
                        </div>
                        <div class="col-8">
                            <input name="region_name" id="region_name" class="form-control" placeholder="Nama Kabupaten"
                                maxlength="80"
                                value="{{ $kabupaten->nama_kabupaten_baru ?? $kabupaten->nama_kabupaten }}" @if(!$kabupaten->nama_kabupaten) 'disabled' @endif required />
                        </div>
                        {{-- @if ($kabupaten->nama_kabupaten_baru && $kabupaten->jenis == false)
                        <div class="col-12">
                            <p><code>Permendagri No. 77 Tahun 2019 : {{ $kabupaten->nama_kabupaten }}</code></p>
                        </div>
                        @endif --}}
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
        })
</script>
@endpush