@extends('layouts.index')

@section('title', 'Riwayat Domain Belum Terpetakan')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Riwayat Domain Belum Terpetakan</small></h1>
@stop

@section('content')
    @include('layouts.components.notification')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-warning">
                <div class="card-header with-border">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Domain yang belum memiliki desa terdaftar ({{ $orphans->total() }} data)
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Data di bawah ini berasal dari pelacakan domain yang kode desanya belum terdaftar pada tabel desa.
                        Admin perlu melakukan pemeriksaan dan pencatatan manual.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode Desa</th>
                                    <th>Domain</th>
                                    <th>Domain Normalized</th>
                                    <th>Source</th>
                                    <th>Diperoleh Pada</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orphans as $orphan)
                                    <tr>
                                        <td>{{ $orphan->kode_desa }}</td>
                                        <td><code>{{ $orphan->domain }}</code></td>
                                        <td>{{ $orphan->domain_normalized }}</td>
                                        <td>{{ $orphan->source }}</td>
                                        <td class="text-nowrap">
                                            {{ $orphan->obtained_at ? $orphan->obtained_at->format('d-m-Y H:i') : '-' }}
                                        </td>
                                        <td>{{ $orphan->note }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada domain yang belum terpetakan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $orphans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
