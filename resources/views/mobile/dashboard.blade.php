@extends('layouts.index')

@section('title', 'Dashboard')

@section('content_header')
    <h1>
        Dashboard<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan Aplikasi Mobile @if ($provinsi = session('provinsi'))
                {{ "| {$provinsi->nama_prov}" }}
            @endif
        </small></h1>
@stop

@section('content')
    @include($baseView.'.summary.desa')
@stop
