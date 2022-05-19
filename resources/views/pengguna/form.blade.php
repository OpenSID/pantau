@extends('layouts.index')

@section('title', 'Tambah Pengguna Baru')

@section('content_header')
    <h1>Tambah Pengguna Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('akun-pengguna.index') }}" class="btn btn-primary float-right">Data
                                Pengguna</a>
                        </div>
                    </div>
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
                            <button class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
