@extends('layouts.index')

@section('title', 'Ubah Data Kecamatan')

@section('content_header')
    <h1>Kecamatan<small class="font-weight-light ml-1 text-md">Ubah Data Kecamatan</small></h1>
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
                            <a href="{{ route('kecamatan.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Kecamatan
                            </a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('kecamatan.update', $kecamatan->id) }}">
                    <div class="card-body">
                        @method('PUT')
                        @csrf

                        <div class="row">
                            <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kode_provinsi" class="form-control" placeholder="00" type="text"
                                    value="{{ $kecamatan->kode_provinsi }}" readonly required />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_provinsi" data-placeholder="Pilih Provinsi"
                                    style="width: 100%;" required>
                                    <option selected>Pilih Provinsi</option>
                                    @if ($kecamatan->kode_provinsi || $kecamatan->nama_provinsi)
                                        <option selected>{{ $kecamatan->nama_provinsi }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kabupaten <span class="required">*</span></label>
                            <div class="col-4">
                                <input id="kode_kabupaten" name="parent_code" class="form-control" placeholder="00.00" type="text"
                                    value="{{ $kecamatan->kode_kabupaten }}" readonly required />
                            </div>
                            <div class="col-8">
                                <select class="form-control" id="list_kabupaten" data-placeholder="Pilih Kabupaten"
                                    style="width: 100%;" @if (!$kecamatan->nama_kabupaten) 'disabled' @endif required>
                                    <option selected>Pilih Kabupaten</option>
                                    @if ($kecamatan->kode_kabupaten || $kecamatan->nama_kabupaten)
                                        <option selected>{{ $kecamatan->nama_kabupaten }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label class="control-label col-sm-12">Kecamatan <span class="required">*</span></label>
                            <div class="col-4">
                                <input name="region_code" id="region_code" class="form-control" placeholder="00.00.00"
                                    data-inputmask="{!! "'mask':'" . $kecamatan->kode_kabupaten . ".99'" !!}" data-mask value="{{ $kecamatan->kode_kecamatan }}"
                                    @if (!$kecamatan->kode_kecamatan) 'disabled' @endif required />
                            </div>
                            <div class="col-8">
                                <input name="region_name" id="region_name" class="form-control" placeholder="Nama Kecamatan"
                                    maxlength="80" value="{{ $kecamatan->nama_kecamatan_baru ?? $kecamatan->nama_kecamatan }}"
                                    @if (!$kecamatan->nama_kecamatan) 'disabled' @endif required />
                            </div>
                            @if ($kecamatan->nama_kecamatan_baru && $kecamatan->jenis == false)
                                <div class="col-12">
                                    <p><code>Permendagri No. 77 Tahun 2019 : {{ $kecamatan->nama_kecamatan }}</code></p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <button type="reset" class="btn btn-sm btn-danger btn-block"><i
                                        class="fas fa-times"></i>
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
