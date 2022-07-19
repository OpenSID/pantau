@extends('layouts.index')

@section('title', 'Impor Data Desa')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Impor Data Desa</small></h1>
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
                                    class="fas fa-arrow-circle-left"></i>&ensp;Kembali Ke Daftar Desa
                            </a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ url('desa/proses-import') }}" enctype="multipart/form-data">
                    <div class="card-body">
                        @csrf

                        <div class="form-group">
                            <label for="InputFile">File Import</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="InputFile"
                                        value="" required>
                                    <label class="custom-file-label" for="InputFile">Pilih File</label>
                                </div>
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
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Panduan</b></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="raw">
                        <div class="form-group">
                        <ul>
                            <li>Data yang digunakan diambil dari <a href="https://github.com/cahyadsn/wilayah/tree/master/db" target="_blank">https://github.com/cahyadsn/wilayah/tree/master/db</a></li>
                            <li>Buka phpMyAdmin atau Lainnya, kemudian jalankan query tsb (import)</li>
                            <li>Lakukan export dengan format .csv</li>
                            <li>
                                Contoh hasil export dari <a href="https://github.com/cahyadsn/wilayah/blob/master/db/wilayah_2022.sql"  target="_blank">https://github.com/cahyadsn/wilayah/blob/master/db/wilayah_2022.sql</a>
                                <br><a href="{{ url('desa/contoh-import') }}" class="btn btn-sm btn-primary" target="_blank">Data Wilayah 2022</a>
                            </li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('/vendor/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
@endpush
