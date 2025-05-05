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
                        $maxTotal = $provinsi_pengguna->max('total');
                        $chunks = $provinsi_pengguna->chunk(16);
                    @endphp
                    @foreach ($chunks[0] ?? [] as $provinsi)
                        <tr>
                            <td>
                                {{ $provinsi->nama_provinsi }}
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($provinsi->total / $maxTotal) * 100 }}%"
                                        aria-valuenow="{{ ($provinsi->total / $maxTotal) * 100 }}" aria-valuemin="0"
                                        aria-valuemax="{{ $provinsi->total }}"></div>
                                    <div class="pt-2"> &nbsp;{{ $provinsi->total }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tbody class="collapse" id="pengguna-lengkap">
                    @foreach ($chunks as $chunk)
                        @if ($loop->first)
                            @continue
                        @endif
                        @foreach ($chunk as $provinsi)
                            <tr>
                                <td>
                                    {{ $provinsi->nama_provinsi }}
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ ($provinsi->total / $maxTotal) * 100 }}%"
                                            aria-valuenow="{{ ($provinsi->total / $maxTotal) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="{{ $provinsi->total }}"></div>
                                        <div class="pt-2"> &nbsp;{{ $provinsi->total }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="mt-4">
                        <td class="text-center w-100" colspan="2">
                            <div class="btn border mt-2">
                                <a href="#pengguna-lengkap" data-toggle="collapse" role="button">Selengkapnya</a>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $('a[href="#pengguna-lengkap"]').click(function() {
                const _text = $(this).text()
                let _defaultText = 'Selengkapnya'
                if (_text == _defaultText) {
                    _defaultText = 'Perkecil'
                }

                $(this).text(_defaultText)
            })
        })
    </script>
@endpush
