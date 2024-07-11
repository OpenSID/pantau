<style>
    .swiper-button-next, .swiper-button-prev {
        width: 50px;
        height: 50px;
        background-color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #007bff;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .swiper-button-next::after, .swiper-button-prev::after {
        font-size: 20px;
    }

    .swiper-button-next:hover, .swiper-button-prev:hover {
        background-color: #dbdbdb;
    }
</style>
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
    $(document).ready(function() {
      var swiper = new Swiper("#swiper", {
        slidesPerView: 2,
        spaceBetween: -75,
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
      });
    });
  </script>    
  @endpush