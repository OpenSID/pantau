@extends('adminlte::page')

@section('footer')
    <strong>Hak cipta © <?= date('Y') ?> <a href="https://opendesa.id">OpenDesa</a>.</strong>
    Seluruh hak cipta dilindungi.
    <div class="float-right d-none d-sm-inline-block">
        <b>Versi</b> {{ pantau_versi() }}
    </div>
@endsection

@include('layouts.components.catatan_rilis')

@push('js')
    <script type="application/javascript">
        $(document).ready(function() {
            window.setTimeout(function() {
                $("#notifikasi").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);
        });

        function filter_open () {
            if ($('a[href="#collapse-filter"]').attr('aria-expanded') == 'false') {
                $('a[href="#collapse-filter"]').trigger('click')
            }
        }

        $.extend($.fn.dataTable.defaults, {
            language: { url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json" }
        });
    </script>
@endpush


@push('css')
<style>
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    min-height: 38px;
    height: 38px;
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    font-size: 1rem;
    line-height: 1.5;
}
.select2-container--default .select2-selection--single .select2-selection__rendered,
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    line-height: 24px;
}
</style>
@endpush
