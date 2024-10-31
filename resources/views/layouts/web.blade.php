@extends('adminlte::page')
@push('css')
<link rel="stylesheet" href="{{ asset('vendor/weblte/custom.css') }}">
@endpush
@section('footer')
    <strong>Hak cipta © <?= date('Y') ?> <a href="https://opendesa.id">OpenDesa</a>.</strong>
    Seluruh hak cipta dilindungi.
    <div class="float-right d-none d-sm-inline-block">
        <b>Versi</b> {{ pantau_versi() }}
    </div>
@endsection
@section('right-sidebar')
    @include('layouts.components.web_right_menu')
@endsection
@push('js')
    <script type="application/javascript">
        $(document).ready(function() {
            const _login = $('nav.main-header>.container>ul.navbar-nav>.nav-item:eq(1)')
            const _burger = $('nav.main-header>.container>ul.navbar-nav>.nav-item:last')
            const _timer = _login.prev('.nav-item')
            @guest
            _login.addClass('bg-blue d-none d-lg-block')
            _login.find('a').html('Login')
            @endguest
            @auth
            $('li.dropdown.user-menu').addClass('pt-2')
            @endauth
            _burger.addClass('d-block d-lg-none pt-1')

            _timer.html(`<div class="mr-1">
            <div class="h3" style="margin-bottom:-5px;font-weight:bold" id="timeClock">00:00:00</div>
            <div class="date">{{ format_daydate(date('Y-m-d')) }}</div>
            </div>`)
            $('nav.main-header>.container>a.navbar-brand>span.brand-text').addClass('d-none d-sm-block float-right')

            setInterval(myTimer, 1000);
            function myTimer() {
                var d = new Date();
                var t = d.toLocaleTimeString().replaceAll('.', ':');
                $("#timeClock").html(t);
            }

            $('a.nav-link[data-widget="control-sidebar"]').click(function () {window.scrollTo({ top: 0, behavior: 'smooth' });})
            
        });

        $.extend($.fn.dataTable.defaults, {
            language: { url: "https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json" }
        });

        function filter_open () {
            if ($('a[href="#collapse-filter"]').attr('aria-expanded') == 'false') {
                $('a[href="#collapse-filter"]').trigger('click')
            }
        }
    </script>
@endpush
