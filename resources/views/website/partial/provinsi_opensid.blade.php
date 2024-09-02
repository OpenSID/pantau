@push('css')
<style>
    .table-custom {
        width: 100%;
    }
    .table-custom td {
        width: 10%;
        border: none;
        padding: 0;
    }
    .table-custom .progress {
        width: 100%;
    }
</style>
@endpush

<div class="container mt-3">
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-responsive table-custom">
                <tbody>
                    @forelse ($provinsi as $item)
                        <tr>
                            <td>
                                {{ $item['nama_prov'] }}
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $item['persentase'] }}%" aria-valuenow="{{ $item['persentase'] }}" aria-valuemin="0" aria-valuemax="100">{{ $item['jumlah_kab'] }}</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    Belum ada data
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>