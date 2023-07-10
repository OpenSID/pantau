@extends('layouts.index')

@section('title', 'Daftar Kabupaten')

@section('content_header')
    <h1>Pengaturan Aplikasi</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <h3 class="card-title">Pengaturan Dasar</h3>
                </div>
                <form method="POST" action="{{ route('pengaturan.aplikasi.store') }}">
                        @csrf
                <div class="card-body">
                    @foreach ($pengaturan as $key => $value)
                        @switch($value->jenis)
                            @case('select')
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-3 col-lg-2" for="{{ $value->key }}">{{ $value->judul }}</label>
                                    <div class="col-sm-12 col-md-3  col-lg-4">
                                        <select class="form-control input-sm select2 required" id="{{ $value->key }}"
                                            name="{{ $value->key }}">
                                            @foreach (json_decode($value->option) as $key_o => $value_o)
                                                <option value="{{ $key_o }}"
                                                    {{ ($value->value == $key_o) ? 'selected' : '' }}>{{ $value_o }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="col-sm-12 col-md-6 col-lg-6">{{ $value->keterangan }}</label>
                                </div>
                            @break
                            @case('select-multiple')
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-3 col-lg-2" for="{{ $value->key }}">{{ $value->judul }}</label>
                                    <div class="col-sm-12 col-md-3  col-lg-4">
                                        <select class="form-control input-sm select2-multiple required"  multiple id="{{ $value->key }}"
                                            @php
                                                $selected = collect(json_decode($value->value))->pluck('key')->toArray();
                                            @endphp
                                            name="{{ $value->key }}[]">
                                            @foreach (json_decode($value->option) as $objOption)
                                                <option value="{{ json_encode($objOption) }}" {{ in_array($objOption->key, $selected) ? 'selected' : '' }} >{{ $objOption->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="col-sm-12 col-md-6 col-lg-6">{{ $value->keterangan }}</label>
                                </div>
                            @break
                            @default
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-3 col-lg-2" for="{{ $value->key }}">{{ $value->judul }}</label>
                                    <div class="col-sm-12 col-md-3 col-lg-4">
                                        <input class="form-control input-sm" id="{{ $value->key }}" name="{{ $value->key }}"
                                            type="text" value="{{ $value->value }}">
                                    </div>
                                    <label class="col-sm-12 col-md-6 col-lg-6"" for="nama">{{ $value->keterangan }}
                                    </label>
                                </div>
                            @break
                        @endswitch
                    @endforeach
                </div>
                <div class="card-footer">

                          <button type="reset" class="btn btn-sm btn-danger "><i class="fas fa-times"></i>
                               Batal</button>

                          <button type="submit" class="btn btn-sm btn-success "><i class="fas fa-save"></i>
                               Simpan</button>

                  </div>
                </form>
              </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(function(){
            $('.select2-multiple').select2();
        })
    </script>
@endsection
@section('css')
    <style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3498db;
    }
    </style>
@endsection


