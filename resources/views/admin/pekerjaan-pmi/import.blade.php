@extends('layouts.index')

@section('title', 'Impor Data Pekerjaan PMI')

@section('content_header')
<h1>Pekerjaan PMI<small class="font-weight-light ml-1 text-md">Impor Data Pekerjaan PMI</small></h1>
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
                                class="fas fa-arrow-circle-left"></i>&ensp;Kembali Ke Daftar Pekerjaan PMI
                        </a>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('pekerjaan-pmi.proses-import') }}" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf

                    <div class="form-group">
                        <label for="InputFile">File Import</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="InputFile" value=""
                                    required>
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
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="raw">
                    <div class="form-group">
                        <ul>
                            <li>
                                Berikut contoh format file yang digunakan <a
                                    href="{{ route('pekerjaan-pmi.contoh-import') }}" class="btn btn-sm btn-primary"
                                    target="_blank">Data Pekerjaan PMI</a>
                            </li>
                            <li>File yang digunakan harus berformat <b>.xlsx</b></li>
                            <li>
                                Kolom yang harus ada:
                                <ul>
                                    <li><strong>nama_pekerjaan</strong> atau <strong>nama</strong> - Nama pekerjaan PMI
                                    </li>
                                </ul>
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