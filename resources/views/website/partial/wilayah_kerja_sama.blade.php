<h4 class="text-white" style="text-align: left">Wilayah Kerja Sama</h4>
<div id="swiper" class="swiper">
    <div class="swiper-wrapper">
        @foreach ($wilayah as $item)
          <div class="swiper-slide">
              <div class="d-flex justify-content-center">
                  <div class="card">
                      <div class="card-body">
                        <a href="{{ url('web/opensid') }}?kode_provinsi={{ $item->kode_prov }}&nama_provinsi={{ $item->nama_prov }}&kode_kabupaten={{ $item->kode_kab }}&nama_kabupaten={{ $item->nama_kab }}&nama_wilayah={{ $item->nama_wilayah }}" class="text-decoration-none">
                          <p class="card-text m-0 text-blue">{{ $item->nama_wilayah }}</p>
                        </a>
                          <p class="card-text m-0">OpenSID</p>
                          <p class="card-text m-0">Terpasang: {{ $item->desa_count }}</p>
                          <p class="card-text m-0">Belum Terpasang: {{ $item->wilayah_count - $item->desa_count }}</p>
                      </div>
                  </div>
              </div>
          </div>
        @endforeach
    </div>
    <!-- Add Arrows -->
    <div class="swiper-pagination"></div>
    <!-- Add Navigation -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>

@push('js')
<script>
    var swiperOptions = {
    slidesPerView: 2,
    spaceBetween: -50,
    slidesPerGroup: 1,
    loop: true,
    loopFillGroupWithBlank: true,
    autoplay: {
        delay: 3000,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
};

var swiper = new Swiper('.swiper-container', swiperOptions);

    $(document).ready(function() {
      function checkScreenWidth() {
        if ($(window).width() < 768) {
          swiperOptions.slidesPerView = 1;
          swiperOptions.spaceBetween = 0;
        } else {
            swiperOptions.slidesPerView = 2;
            swiperOptions.spaceBetween = -50;
        }
      }

      var swiper = new Swiper("#swiper", swiperOptions);
      checkScreenWidth();

      $(window).on('resize', function() {
        checkScreenWidth();
        swiper.destroy();
        swiper = new Swiper("#swiper", swiperOptions);
      });
    });
  </script>
@endpush
