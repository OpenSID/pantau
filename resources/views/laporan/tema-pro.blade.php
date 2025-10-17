@extends('layouts.index')
@include('layouts.components.select2_wilayah')

@section('title', 'Desa Pengguna Tema Pro')

@section('content_header')
<h1>
    Dasbor<small class="font-weight-light ml-1 text-md font-weight-bold">Status Penggunaan Tema Pro
    </small></h1>
@stop

@section('content')    
    @include('layouts.components.notification')
    <div class="row">
        @if(empty(request()->query('tema')))
            <div class="col-lg-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <div class="row">
                            @php $colorIndex = 0; @endphp
                            @php $colors = ['bg-info', 'bg-success', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-dark']; @endphp

                            @foreach($temaProList as $item)
                                <div class="col-lg-4">
                                    <!-- small card -->
                                    <div class="small-box {{ $colors[$colorIndex % count($colors)] }}">
                                        <div class="inner">
                                            <h3>{{ $item->total }}</h3>
                                            <p>Tema {{ ucfirst($item->tema_nama) }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-palette"></i>
                                        </div>
                                        <a href="{{ url('laporan/tema-pro') }}?tema={{ $item->tema_nama }}"
                                            class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                @php $colorIndex++; @endphp
                            @endforeach

                            @if(count($temaProList) == 0)
                                <div class="col-lg-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Tidak ada data tema pro yang tersedia.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Desa Pengguna Tema Pro
                        {{request()->query('tema') ? '- ' . request()->query('tema') : ''}}
                    </h3>
                    <div class="card-tools">
                        <span data-toggle="tooltip" title="" class="badge badge-primary">
                            @if(request()->query('tema'))
                                @foreach($temaProList as $item)
                                    @if($item->tema_nama == request()->query('tema'))
                                        {{$item->total}}
                                        @break
                                    @endif
                                @endforeach
                            @else
                                {{$temaPro}}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table-desa">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Terpantau</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Web</th>
                                    <th>Tema</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var desa = $('#table-desa').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ url('laporan/tema-pro') }}{{request()->query('tema') ? '?tema=' . request()->query('tema') : ''}}`,
                method: 'get'
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'updated_at'
            },
            {
                data: 'nama_desa'
            },
            {
                data: 'nama_kecamatan'
            },
            {
                data: 'nama_kabupaten'
            },
            {
                data: 'nama_provinsi',
            },
            {
                data: 'url_hosting'
            },
            {
                data: 'tema'
            },
            ],
            order: [
                [1, 'desc']
            ],
        });
    </script>
@endsection