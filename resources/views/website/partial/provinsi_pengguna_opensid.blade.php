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
                    @php
                    $maxTotal = $provinsi_pengguna_opensid->max('total')
                    @endphp
                    @foreach($provinsi_pengguna_opensid as $provinsi)
                    <tr>
                        <td>
                            {{ $provinsi->nama_provinsi }}
                        </td>
                        <td>                            
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $provinsi->total/$maxTotal * 100 }}%" aria-valuenow="{{ $provinsi->total/$maxTotal * 100 }}" aria-valuemin="0" aria-valuemax="{{ $provinsi->total }}"></div>
                                <div class="pt-2"> &nbsp;{{ $provinsi->total }}</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach                    
                </tbody>
            </table>
        </div>
    </div>
</div>