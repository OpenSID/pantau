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
    
    function updateData(){
        const params = {period : $('input[name=periods]').val(), provinsi : $('select[name=provinsi]').val(), kabupaten : $('select[name=kabupaten]').val(), kecamatan : $('select[name=kecamatan]').val()}        

        $.ajax({
            url: 'api/web/summary',
            data: params,
            type: "GET",
            beforeSend: function(){
                $('#box-provinsi>.total').text('..')
                $('#box-kabupaten>.total').text('..')
                $('#box-kecamatan>.total').text('..')
                $('#box-desa>.total').text('..')
            },
            success: function(data) {
                const total = data.total
                const detail = data.detail
                $('#box-provinsi>.total').text(total.provinsi.total)
                $('#box-provinsi span.pertumbuhan').html(`<a href="#" class="${total.provinsi.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.provinsi.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.provinsi.pertumbuhan}</span></a>`)
                $('#box-kabupaten>.total').text(total.kabupaten.total)
                $('#box-kabupaten span.pertumbuhan').html(`<a href="#" class="${total.kabupaten.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.kabupaten.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.kabupaten.pertumbuhan}</span></a>`)
                $('#box-kecamatan>.total').text(total.kecamatan.total)
                $('#box-kecamatan span.pertumbuhan').html(`<a href="#" class="${total.kecamatan.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.kecamatan.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.kecamatan.pertumbuhan}</span></a>`)
                $('#box-desa>.total').text(total.desa.total)
                $('#box-desa span.pertumbuhan').html(`<a href="#" class="${total.desa.pertumbuhan < 0 ? 'text-red' : 'text-green'}"><i
                                    class="fa ${total.desa.pertumbuhan < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                                ${total.desa.pertumbuhan}</span></a>`)
                                
                
                let _listElm;
                for(let i in detail){
                    _listElm = $(`#${i}-baru`).find('ol')
                    _listElm.empty()
                    for(let j in detail[i]){
                        _listElm.append(`<li>${detail[i][j]}</li>`)
                    }
                }


                $.ajax({
                    url: 'api/web/chart-usage',
                    data: params,
                    type: "GET",            
                    success: function(data) {
                        myChart.data = data;
                        myChart.update();
                    }
                }, 'json')
            }
        }, 'json')
    }

    $(document).ready(function() {
        $('#filter').click(function(){
            updateData()
        })
        $('input[name=periods]').change(function(){
            updateData()
        })
        $('#reset').click(function(){
            $('#collapse-filter select').val('')
        })

        updateData()
    })
</script>
@endpush