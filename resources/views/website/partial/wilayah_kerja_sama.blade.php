<h4 class="text-white" style="text-align: center">Wilayah Kerja Sama</h4>
<div id="swiper" class="swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <div class="d-flex justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text m-0 text-blue">Kabupaten Bima</p>
                        <p class="card-text m-0">OpenSID</p>
                        <p class="card-text m-0">Terpasang: 20</p>
                        <p class="card-text m-0">Belum Terpasang: 20</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="d-flex justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text m-0 text-blue">Kabupaten Lainnya</p>
                        <p class="card-text m-0">OpenSID</p>
                        <p class="card-text m-0">Terpasang: 20</p>
                        <p class="card-text m-0">Belum Terpasang: 20</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-slide">
            <div class="d-flex justify-content-center">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text m-0 text-blue">Kota Bima</p>
                        <p class="card-text m-0">OpenSID</p>
                        <p class="card-text m-0">Terpasang: 20</p>
                        <p class="card-text m-0">Belum Terpasang: 20</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>
  
@push('js')
<script>
    // Definisikan swiperOptions di luar scope $(document).ready()
    var swiperOptions = {
      slidesPerView: 2,
      spaceBetween: -50,
      slidesPerGroup: 1,
      loop: true,
      loopFillGroupWithBlank: true,
      // autoplay: {
      //     delay: 3000,
      // },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    };
  
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