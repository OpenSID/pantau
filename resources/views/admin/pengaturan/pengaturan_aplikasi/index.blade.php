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
                                        <label class="col-sm-12 col-md-3 col-lg-3"
                                            for="{{ $value->key }}">{{ $value->judul }}</label>
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <select class="form-control input-sm select2 required" id="{{ $value->key }}"
                                                name="{{ $value->key }}">
                                                @foreach (json_decode($value->option) as $key_o => $value_o)
                                                    <option value="{{ $key_o }}"
                                                        {{ $value->value == $key_o ? 'selected' : '' }}>{{ $value_o }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="col-sm-12 col-md-3 col-lg-3">{{ $value->keterangan }}</label>
                                    </div>
                                @break

                                @case('select-multiple')
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-md-3 col-lg-3"
                                            for="{{ $value->key }}">{{ $value->judul }}</label>
                                        <div class="col-sm-12 col-md-6  col-lg-6">
                                            <select class="form-control input-sm select2-multiple required" multiple
                                                id="{{ $value->key }}"
                                                @php $selected = collect(json_decode($value->value))->pluck('key')->toArray(); @endphp
                                                name="{{ $value->key }}[]">
                                                @foreach (json_decode($value->option) as $objOption)
                                                    <option value="{{ json_encode($objOption) }}"
                                                        {{ in_array($objOption->key, $selected) ? 'selected' : '' }}>
                                                        {{ $objOption->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="col-sm-12 col-md-3 col-lg-3">{{ $value->keterangan }}</label>
                                    </div>
                                @break
                                @case('select-tag')
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-md-3 col-lg-3"
                                            for="{{ $value->key }}">{{ $value->judul }}</label>
                                        <div class="col-sm-12 col-md-6  col-lg-6">
                                            <select class="form-control input-sm select2-tag required" multiple
                                                id="{{ $value->key }}"
                                                name="{{ $value->key }}[]">
                                                @if (! empty($value->value))
                                                    @foreach (explode('|',$value->value) as $objOption)
                                                        <option value="{{ $objOption }}" selected >{{ $objOption }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <label class="col-sm-12 col-md-3 col-lg-3">{{ $value->keterangan }}</label>
                                    </div>
                                @break
                                @default
                                    <div class="form-group row" id="form_{{ $value->key }}">
                                        <label class="col-sm-12 col-md-3 col-lg-3"
                                            for="{{ $value->key }}">{{ $value->judul }}</label>
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <input class="form-control input-sm" id="{{ $value->key }}"
                                                name="{{ $value->key }}" type="{{ $value->jenis }}" value="{{ $value->value }}">
                                        </div>
                                        <label class="col-sm-12 col-md-3 col-lg-3" for="nama">{{ $value->keterangan }}
                                        </label>
                                    </div>
                                @break
                            @endswitch
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="reset" class="btn btn-sm btn-danger "><i class="fas fa-times"></i>Batal</button>
                        <button type="submit" class="btn btn-sm btn-success "><i class="fas fa-save"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(function() {
            $('.select2-multiple').select2();
            $('.select2-tag').select2({
                tags: true,
                tokenSeparators: [',', '|', ' ']
            });
        })

        showStorage($('#cloud_storage').val());

        $('#cloud_storage').on('change', function (e) {
            showStorage($('#cloud_storage').val());
        });

        function showStorage(value) {
            if (value == 0) {
                $('#form_waktu_backup').hide();
                $('#form_maksimal_backup').hide();
                $('#form_akhir_backup').hide();
                $('#waktu_backup').removeClass('required');
                $('#maksimal_backup').removeClass('required');
                $('#akhir_backup').removeClass('required');
            } else {
                $('#form_waktu_backup').show();
                $('#form_maksimal_backup').show();
                $('#form_akhir_backup').show();
                $('#waktu_backup').addClass('required');
                $('#maksimal_backup').addClass('required');
                $('#akhir_backup').addClass('required');
            }
        }
    </script>
@endsection
@section('css')
    <style>
        .select2 {
            width: 100% !important;
        }

        .select2-container {
            display: block !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3498db;
        }
    </style>
@endsection
