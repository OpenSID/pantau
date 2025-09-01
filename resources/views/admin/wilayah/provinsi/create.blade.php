@extends('layouts.index')

@section('title', 'Tambah Data Provinsi')

@section('content_header')
<h1>Provinsi<small class="font-weight-light ml-1 text-md">Tambah Data Provinsi</small></h1>
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
                        <a href="{{ route('provinsi.index') }}" class="btn btn-sm btn-block btn-secondary"><i
                                class="fas fa-arrow-circle-left"></i> Kembali Ke Daftar Provinsi
                        </a>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('provinsi.store') }}">
                <div class="card-body">
                    @method('POST')
                    @csrf

                    <div class="row">
                        <label class="control-label col-sm-12">Provinsi <span class="required">*</span></label>
                        <div class="col-4">
                            <input name="region_code" id="region_code" class="form-control" placeholder="00"
                                data-inputmask="{!! "'mask' :'99'" !!}" data-mask required />
                        </div>
                        <div class="col-8">
                            <input name="region_name" id="region_name" class="form-control" placeholder="Nama Provinsi"
                                maxlength="80" required />
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
@push('js')
<script src="{{ asset('/vendor/inputmask/jquery.inputmask.js') }}"></script>
<script>
    $(function() {
            $('[data-mask]').inputmask();
        })
</script>
@endpush