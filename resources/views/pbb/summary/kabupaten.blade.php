<div class="card card-outline card-info col-lg-12">
    <div class="card-header">
        <h3 class="card-title">Kabupaten Pengguna</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            @foreach ($kabupatenWidgets as $widget)
                @include('widget.card', $widget)
            @endforeach
        </div>
    </div>
    <!-- /.card-body -->
</div>
