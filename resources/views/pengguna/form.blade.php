@extends('layouts.index')

@section('title', 'Tambah Pengguna')

@section('content_header')
    <h1>Pengguna<small class="font-weight-light ml-1 text-md">Tambah Pengguna</small></h1>
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
                            <a href="{{ route('akun-pengguna.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                    class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('akun-pengguna.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Group <span class="text-danger">*</span></label>
                            <select name="id_grup" class="form-control">
                                <option value="1">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control">
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
