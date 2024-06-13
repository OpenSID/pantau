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

@push('js')
    <script type="application/javascript">
        $(document).ready(function() {
            const _login = $('nav.main-header>.container>ul.navbar-nav>.nav-item:last')
            const _timer = _login.prev('.nav-item')
            _login.addClass('bg-blue')
            _login.find('a').html('Login')

            _timer.html(`<div class="mr-1">
            <div class="h3" style="margin-bottom:-5px;font-weight:bold" id="timeClock">00:00:00</div>
            <div class="date">{{ format_daydate(date('Y-m-d')) }}</div>
            </div>`)

            setInterval(myTimer, 1000);
            function myTimer() {
                var d = new Date();
                var t = d.toLocaleTimeString().replaceAll('.', ':');
                $("#timeClock").html(t);
            }
            
        });

        function filter_open () {
            if ($('a[href="#collapse-filter"]').attr('aria-expanded') == 'false') {
                $('a[href="#collapse-filter"]').trigger('click')
            }
        }
    </script>
@endpush
