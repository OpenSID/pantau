@extends('layouts.index')

@section('title', 'Tambah Notifikasi')

@section('content_header')
    <h1>Ubah Notifikasi</h1>
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
                            <a href="{{route('notifikasi.index')}}" class="btn btn-primary float-right">Data Notifikasi</a>
                        </div>
                    </div>
                    <form method="post" action="{{route('notifikasi.update', ['id'=>$data->id])}}">
                        @csrf
                        <div class="form-group">
                            <label>Aktif <span class="text-danger">*</span></label>
                            <select name="aktif" class="form-control">
                                <option value="1" {{$data->aktif == 1 ? "selected" : ""}}>Aktif</option>
                                <option value="0" {{$data->aktif == 0 ? "selected" : ""}}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Frekuensi <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-11">
                                    <input type="text" name="frekuensi" class="form-control" placeholder="Contoh: setiap 30, 60, 90 hari" value="{{$data->frekuensi}}">
                                </div>
                                <div class="col-md-1">
                                    Hari
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" value="{{$data->kode}}">
                        </div>
                        <div class="form-group">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" value="{{$data->judul}}">
                        </div>
                        <div class="form-group">
                            <label>Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-control">
                                <option value="">Pilih Jenis Notifikasi</option>
                                <option value="pemberitahuan" {{$data->jenis == 'pemberitahuan' ? "selected" : ""}}>Pemberitahuan</option>
                                <option value="pengumuman" {{$data->jenis == 'pengumuman' ? "selected" : ""}}>Pengumuman</option>
                                <option value="peringatan" {{$data->jenis == 'peringatan' ? "selected" : ""}}>Peringatan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Server <span class="text-danger">*</span></label>
                            <select name="input_server" class="form-control">
                                <option value="TrackSID" {{$data->server == 'TrackSID' ? "selected" : ""}}>TrackSID</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Isi <span class="text-danger">*</span></label>
                            <textarea name="isi" class="form-control" rows="5">{{$data->isi}}</textarea>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
