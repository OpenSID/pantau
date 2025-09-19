@extends('layouts.index')

@section('title', 'Dasbor')

@section('content_header')
    <h1>
        Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan Aplikasi KelolaDesa @if ($provinsi = session('provinsi'))
                {{ "| {$provinsi->nama_prov}" }}
            @endif
        </small></h1>
@stop

@section('content')
    @include($baseView.'.summary.desa')
@stop
