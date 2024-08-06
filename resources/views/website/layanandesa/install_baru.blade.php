<img src="{{ asset('assets/img/opensid_logo.png') }}" width="20" alt="Logo">
<span class="text-black">Daftar Desa Baru Install</span>
<div class="container">
    <div class="marquee">
        <div class="track-vertical w-100">
            <div class="flex-vertical marquee-fix">
                @forelse($installHariIni as $item)
                <div class="mt-3 w-100 block-info">
                    <div class="d-flex justify-content-between">
                        <div class="text-wrap">
                            <p class="m-0">{{ $item->desa->nama_desa }}</p>
                            <p class="m-0">{{ $item->desa->nama_provinsi }}, {{ $item->desa->nama_kabupaten }}
                            </p>
                            <p class="m-0">INSTALL {{ $item->created_at->format('H:i') }} | versi {{ $item->versi}}</p>
                        </div>
                        <div class="p-0"><span
                                class="badge badge-success text-wrap">{{ formatDateTimeForHuman($item->created_at) }}</span>
                        </div>
                    </div>
                </div>
                <hr>
                @empty
                <div class="text-wrap text-center mt-5">Tidak ada desa yang memasang LayananDesa hari ini</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
   .block-info p {
    font-size: 80%;
   } 
   .block-info .badge {
    font-size: 60%;
   } 
  .marquee {
      position: relative;
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      overflow: hidden;
      width: 100%;
      height: 405px;
      -webkit-box-orient: horizontal;
      -webkit-box-direction: normal;
      -webkit-flex-direction: row;
      -ms-flex-direction: row;
      flex-direction: row;
      -webkit-box-pack: center;
      -webkit-justify-content: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: start;
      -webkit-align-items: flex-start;
      -ms-flex-align: start;
      align-items: flex-start;
      -webkit-box-flex: 1;
      -webkit-flex: 1;
      -ms-flex: 1;
      flex: 1;
    }
    .track-vertical {
        position: absolute;
        white-space: nowrap;
        will-change: transform;        
        animation: marquee-vertical 100s linear infinite;        
        /* manipulate the speed of the marquee by changing "20s" line above*/
    }

    @keyframes marquee-vertical {
        from {
            transform: translateY(0);
        }

        to {
            transform: translateY(-50%);
        }
    }
</style>
@endpush