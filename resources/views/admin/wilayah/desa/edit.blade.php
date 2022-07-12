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
                            <a href="{{ url('desa') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ url('desa/store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-3 col-xs-12">Provinsi <span
                                    class="required">*</span></label>
                            <div class="col-md-2 col-sm-3 col-xs-12">
                                <input id="provinsi_id" class="form-control" placeholder="00" type="text" readonly
                                    value="" />
                                <input id="nama_provinsi" type="hidden" name="nama_provinsi" value="" />
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12">
                                <select class="form-control" id="list_provinsi" name="provinsi_id" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-3 col-xs-12">Kabupaten <span
                                    class="required">*</span></label>
                            <div class="col-md-2 col-sm-3 col-xs-12">
                                <input id="kabupaten_id" class="form-control" placeholder="00.00" type="text" readonly
                                    value="" />
                                <input id="nama_kabupaten" type="hidden" name="nama_kabupaten" value="" />
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12">
                                <select class="form-control" id="list_kabupaten" name="kabupaten_id" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Kabupaten</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-3 col-xs-12">Kecamatan <span
                                    class="required">*</span></label>
                            <div class="col-md-2 col-sm-3 col-xs-12">
                                <input id="kecamatan_id" class="form-control" placeholder="00.00.00" type="text" readonly
                                    value="" />
                                <input id="nama_kecamatan" type="hidden" name="nama_kecamatan" value="" />
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12">
                                <select class="form-control" id="list_kecamatan" name="kecamatan_id"
                                    data-placeholder="Pilih kecamatan" style="width: 100%;">
                                    <option selected value="" disabled>Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label>Pilih Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi" class="form-control" required>
                                @foreach ($daftarProvinsi as $key => $item)
                                    <option value="{{ $key }}">{{ $key }} | {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten" class="form-control" required>
                                @foreach ($daftarKabupaten as $key => $item)
                                    <option value="{{ $key }}">{{ $key }} | {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Kecamatan <span class="text-danger">*</span></label>
                            <select name="kecamatan" class="form-control" required>
                                @foreach ($daftarKecamatan as $key => $item)
                                    <option value="{{ $key }}">{{ $key }} | {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="form-group">
                            <label>Kode Desa <span class="text-danger">*</span></label>
                            <input name="kode_desa" class="form-control" value="{{ $desa->kode_desa }}" required />
                        </div>

                        <div class="form-group">
                            <label>Nama Desa <span class="text-danger">*</span></label>
                            <input name="nama_desa" class="form-control" value="{{ $desa->nama_desa }}" required />
                        </div>

                        <div class="form-group">
                            <button class="btn btn-danger">Batal</button>
                            <button class="btn btn-success float-right">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('admin.wilayah.desa.edit')
@push('scripts')
@endpush
