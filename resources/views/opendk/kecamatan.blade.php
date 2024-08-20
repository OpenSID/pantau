@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa OpenSID')

@section('content_header')
    <h1>Kecamatan OpenDK<small class="font-weight-light ml-1 text-md font-weight-bold">(Kecamatan yang memasang OpenDK ) @if($provinsi = session('provinsi')) {{ "| {$provinsi->nama_prov}" }} @endif</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="btn btn-sm btn-secondary" data-toggle="collapse" href="#collapse-filter" role="button"
                                aria-expanded="false" aria-controls="collapse-filter">
                                <i class="fas fa-filter"></i>
                            </a>
                            <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('opendk/kecamatan') }}"><i class="fas fa-file-excel"></i> Excels<a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.components.form_filter')
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table-versi">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Versi</th>
                                    <th>Akses Terakhir</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
            </div>
        </div>
    </div><br><br>
@endsection

@section('js')
<script>
    const params = new URLSearchParams(window.location.search);
    const listVersi = {!! json_encode($listVersi) !!}

    for(var i in listVersi) {
        $('#versi_opendk').append('<option>'+listVersi[i]+'</option>')
    }

    if (params.get('akses_opendk') || params.get('versi_opendk')) {
        if (params.get('versi_opendk')) {
            $('#versi_opendk').val(params.get('versi_opendk')).change();
        }
        if (params.get('akses_opendk')) {
            $('#akses_opendk').val(params.get('akses_opendk')).change();
        }

        filter_open();
    }

    var kecamatan = $('#table-versi').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ordering: true,
        ajax: {
            url: `{{ url('opendk/kecamatan') }}`,
            method: 'get',
            data: function(data) {
                    data.kode_provinsi = $('#provinsi').val() ? $('#provinsi').val() : params.get('kode_provinsi');
                    data.kode_kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : params.get('kode_kabupaten');
                    data.kode_kecamatan = $('#kecamatan').val();
                    data.akses_opendk = $('#akses_opendk').val();
                    data.versi_opendk = $('#versi_opendk').val();
                }
        },
        columns: [
            {
                 orderable: false,
                name: 'url',
                data: function (data) {
                        return `<a href="#" class="more"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>`
                    },
            },
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'nama_kecamatan',
                orderable: false
            },
            {
                data: 'nama_kabupaten',
                orderable: false
            },
            {
                data: 'nama_provinsi',
                orderable: false
            },
            {
                 orderable: false,
                name: 'url',
                data: function (data) {
                        return `<a target="_blank" href="https://${data.url}">https://${data.url}</a>`
                    },
            },
            {
                data: 'versi',
                orderable: true
            },
            {
                data: 'format_updated_at',
                orderable: true
            },

        ],
        "drawCallback": function(settings) {
            $('body').find('.more').click(function (e) {
                e.preventDefault();
                var tr = $(this).closest('tr');
                var row = kecamatan.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.find('a.more').html('<i class="fa fa-plus-circle" aria-hidden="true"></i>');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.find('a.more').html('<i class="fa fa-minus-circle" aria-hidden="true"></i>');
                }
            });
        },


    })

    function format(d) {
        console.log(d)
        // `d` is the original data object for the row
        return (`

            <table cellpadding="2" cellspacing="0" border="0" style="padding-left:1rem;">
                <tbody>
                    <tr>
                        <td style="border: 0px" rowspan="4">Batas Wilayah</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">Utara - ${(d.batas_wilayah == null)? '' : d.batas_wilayah.bts_wil_utara ?? ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">Barat - ${(d.batas_wilayah == null)? '' : d.batas_wilayah.bts_wil_barat ?? ''}</td>
                    </tr>

                    <tr>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">Timur - ${(d.batas_wilayah == null)? '' : d.batas_wilayah.bts_wil_timur ?? ''}</td>
                    </tr>

                    <tr>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">Selatan - ${(d.batas_wilayah == null)? '' : d.batas_wilayah.bts_wil_selatan ?? ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Jumlah Desa</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.jml_desa}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Jumlah Desa Tersinkronisasi</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.jumlahdesa_sinkronisasi}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Jumlah Penduduk</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.jumlah_penduduk}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Jumlah KK</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.jumlah_keluarga}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Jumlah Program Bantuan</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.jumlah_bantuan}</td>
                    </tr>
                    <tr>
                        <td style="border: 0px">Alamat Kantor</td>
                        <td style="border: 0px">:</td>
                        <td style="border: 0px">${d.alamat}</td>
                    </tr>



                </tbody>
            </table>
        `);
    }

    $('#filter').on('click', function(e) {
        kecamatan.draw();
    });

    $(document).on('click', '#reset', function(e) {
        e.preventDefault();
        $('#provinsi').val('').change();
        $('#kabupaten').val('').change();
        $('#kecamatan').val('').change();
        $('#versi_opendk').val('0').change();
        $('#akses_opendk').val('0').change();

        kecamatan.ajax.reload();
    });
    $('#btn-export').click(function(){
        const _href = $(this).data('href')
        window.location.href = _href+'?excel=1&params=' + JSON.stringify($('#table-versi').DataTable().ajax.params())
    })
</script>
@endsection
