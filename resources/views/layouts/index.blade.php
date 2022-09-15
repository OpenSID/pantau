@extends('adminlte::page')

@section('footer')
    <strong>Hak cipta © <?= date('Y') ?> <a href="https://opendesa.id">OpenDesa</a>.</strong>
    Seluruh hak cipta dilindungi.
@endsection
@section('content_top_nav_right')
    <li class="nav-item">
        <div class="row mb-0" style="min-width: 16rem;">
            <label for="staticEmail" class="col-sm-4 col-form-label col-sm-4 d-none d-sm-block col-form-label">Wilayah :</label>
            <div class="col-sm-8 mp-2">
                    <select class="custom-select mb-0 text-light border-0 bg-white" id="wilayah">
                        <option class="bg-white" value="hapus">Semua</option>
                        <option class="bg-lightblue" value="provinsi/52" @selected(session('provinsi') !== null && session('provinsi')['kode_prov'] == '52')>NTB</option>
                    </select>
            </div>
        </div>

    </li>
@endsection



@push('js')
    <script type="application/javascript">
        $(document).ready(function() {
            window.setTimeout(function() {
                $("#notifikasi").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);

            $('#wilayah').change(function (e) {
                e.preventDefault();
                console.log($(this).val());
                var url = $(this).val();
                var base_url = `{{ url('/') }}/sesi/${url}`;
                console.log(base_url)
                window.location = base_url;
            });
        });
    </script>
@endpush
