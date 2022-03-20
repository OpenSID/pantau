@extends('layouts.index')

@section('title', 'Tambah Notifikasi')

@section('content_header')
    <h1>Tambah Notifikasi</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('notifikasi.index')}}" class="btn btn-primary float-right">Data Notifikasi</a>
                        </div>
                    </div>
                    <form method="post" action="{{route('notifikasi.store')}}">
                        @csrf
                        <div class="form-group">
                            <label>Aktif <span class="text-danger">*</span></label>
                            <select name="aktif" class="form-control">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Frekuensi <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-11">
                                    <input type="text" name="frekuensi" class="form-control" placeholder="Contoh: setiap 30, 60, 90 hari">
                                </div>
                                <div class="col-md-1">
                                    Hari
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-control">
                                <option value="">Pilih Jenis Notifikasi</option>
                                <option value="pemberitahuan">Pemberitahuan</option>
                                <option value="pengumuman">Pengumuman</option>
                                <option value="peringatan">Peringatan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Server <span class="text-danger">*</span></label>
                            <select name="input_server" class="form-control">
                                <option value="TrackSID">TrackSID</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Isi <span class="text-danger">*</span></label>
                            <textarea name="isi" class="form-control" rows="5"></textarea>
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
