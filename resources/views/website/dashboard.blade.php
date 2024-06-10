@extends('layouts.web')
@include('layouts.components.select2_wilayah')
@section('title', 'Dasbor')

@section('content_header')

@stop

@section('content')
@include('layouts.components.daterangepicker')
<div class="row">
    <div class="col-12">
        <div class="card bg-gray-light">
            <!-- /.card-header -->
            <div class="card-header">
                <div class="float-left">
                    @can('create-master-pelanggan')
                    <div class="btn-group">
                        <a href="{{ route('pelanggan.create') }}" type="button"
                            class="btn btn-sm btn-block btn-primary">
                            Tambah
                        </a>
                    </div>
                    @endcan
                    <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                        aria-expanded="false" aria-controls="collapse-filter">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
                <div class="float-right">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        </div>
                        <input type="text" name="periods" class="form-control datepicker"
                            data-option='{!! json_encode(array_merge(config("local.daterangepicker"), config("local.daterangepicker_range"), ["autoApply" => false, "singleDatePicker" =>false])) !!}'
                            autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        
                            <form name="filter" method="GET">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('layouts.components.form_filter')
                                    </div>
                                </div>                                
                            </form>
                        
                    </div>
                </div>
                @include('website.partial.summary')
                @include('website.partial.chart')
                @include('website.partial.opensid_baru')
                @include('website.partial.tanpa_opensid')
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@stop

@push('js')
<script>
    $('.datepicker').each(function () {
        const _options = $(this).data('option')
        $(this).daterangepicker(_options)
    })    
</script>
@endpush
