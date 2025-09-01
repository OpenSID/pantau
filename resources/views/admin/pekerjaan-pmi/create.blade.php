@extends('layouts.index')

@section('title', 'Tambah Data Pekerjaan PMI')

@section('content_header')
<h1>Pekerjaan PMI<small class="font-weight-light ml-1 text-md">Tambah Data Pekerjaan PMI</small></h1>
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
                        <a href="{{ route('pekerjaan-pmi.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Pekerjaan PMI
                        </a>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('pekerjaan-pmi.store') }}">
                <div class="card-body">
                    @method('POST')
                    @csrf
                    <div class="form-group">
                        <label class="control-label col-12">Nama Pekerjaan PMI <span class="required">*</span></label>
                        <div class="col-12">
                            <input class="form-control" type="text" name="nama" id="nama"
                                placeholder="Nama Pekerjaan PMI" required>
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