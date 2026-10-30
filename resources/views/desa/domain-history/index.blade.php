@extends('layouts.index')

@section('title', 'Riwayat Domain')

@section('content_header')
    <h1>Desa<small class="font-weight-light ml-1 text-md">Riwayat Domain</small></h1>
@stop

@section('content')
    @include('layouts.components.notification')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header with-border">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="card-title">
                                {{ $desa->nama_desa }} ({{ $desa->kode_desa }})
                            </h3>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('desa.domain-history.download', $desa) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Unduh CSV
                            </a>
                            @can('is-admin')
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-tambah-domain">
                                <i class="fas fa-plus"></i> Catat Domain Baru
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Domain saat ini:
                        <strong>{{ $desa->domain ?: '-' }}</strong>
                        @if ($desa->domain_updated_at)
                            <small>(diperbarui {{ $desa->domain_updated_at->format('d-m-Y H:i') }})</small>
                        @endif
                    </p>
                    <p class="text-muted">
                        <a href="{{ route('desa.domain-history.orphan') }}" class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Lihat domain yang belum terpetakan ke desa
                        </a>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>Domain Normalized</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Diperoleh Pada</th>
                                    <th>Catatan</th>
                                    @can('is-admin')
                                    <th width="12%">Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($histories as $history)
                                    <tr>
                                        <td><code>{{ $history->domain }}</code></td>
                                        <td>{{ $history->domain_normalized }}</td>
                                        <td>
                                            @if ($history->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>{{ $history->source }}</td>
                                        <td class="text-nowrap">
                                            {{ $history->obtained_at ? $history->obtained_at->format('d-m-Y H:i') : '-' }}
                                        </td>
                                        <td>{{ $history->note }}</td>
                                        @can('is-admin')
                                        <td>
                                            @if (! $history->is_active && $history->desa_id)
                                                <form action="{{ route('desa.domain-history.set-active', [$desa, $history]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-warning"
                                                        onclick="return confirm('Jadikan domain ini sebagai domain aktif?')">
                                                        Set Active
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada riwayat domain.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('is-admin')
    <div class="modal fade" id="modal-tambah-domain" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('desa.domain-history.store', $desa) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Catat Domain Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="domain">Domain <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="domain" name="domain" required
                                value="{{ old('domain') }}" placeholder="contoh: desa.example.id">
                        </div>
                        <div class="form-group">
                            <label for="note">Catatan</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection
