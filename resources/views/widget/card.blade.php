<div class="{{ $classWidget }}">
    <!-- small card -->
    <div class="small-box {{ $classBackgroundWidget }}">
        <div class="inner">
            <h3>{{ $totalWidget }}</h3>
            <p>{{ $titleWidget }}</p>
        </div>
        <div class="icon">
            <i class="fas {{ $iconWidget }}"></i>
        </div>
        @if (!empty($urlWidget))
            <a href="{{ $urlWidget }}" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
        @else
            <a href="#" class="small-box-footer">&nbsp;</a>
        @endif

    </div>
</div>
