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
                            <label>Pilih Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi" class="form-control" required>
                                <option value="1">Kode | Nama Provinsi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten" class="form-control" required>
                                <option value="1">Kode | Nama Kabupaten</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Kecamatan <span class="text-danger">*</span></label>
                            <select name="kecamatan" class="form-control" required>
                                <option value="1">Kode | Nama Kecamatan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kode Desa <span class="text-danger">*</span></label>
                            <input name="kode_desa" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label>Nama Desa <span class="text-danger">*</span></label>
                            <input name="nama_desa" class="form-control" required />
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
