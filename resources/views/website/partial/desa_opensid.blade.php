<img src="{{ asset('assets/img/opensid_logo.png') }}" width="20" alt="Logo">
<span class="text-black">Daftar Desa Baru Install</span>
<div class="row">
    <div class="col-md-6 mt-2">
        <div class="small-box bg-green m-0">
            <div class="inner text-center p-0">
                <p class="text-white m-0">{{ $total['online'] }} <br> Online</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-2">
        <div class="small-box bg-red m-0">
            <div class="inner text-center p-0">
                <p class="text-white m-0">{{ $total['offline'] }} <br> Offline</p>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="container marquee" style="height: 250px;">
    <div class="track-vertical w-100 pr-4">
        <div class="flex-vertical marquee-fix">          
          @forelse($installHariIni as $item)
            <div class="mt-3 w-100 block-info">
                <div class="row">
                    <div class="col-10 text-wrap">
                        <h6 class="m-0">{{ $item->nama_desa }}</h6>
                        <p class="m-0" style="font-size: 14px;">{{ $item->nama_provinsi }}, {{ $item->nama_kabupaten }}
                        </p>
                        <p class="m-0" style="font-size: 14px;">INSTALL {{ $item->created_at->format('H:i') }} | versi
                            {{ $item->versi_hosting ?? $item->versi_lokal}}</p>
                    </div>
                    <div class="col-2"><span class="badge badge-{{ $item->versi_hosting ? 'success' : 'danger' }}">{{ $item->versi_hosting ? 'Online' : 'Offline' }}</span></div>
                </div>
            </div>
            <hr>
          @empty          
          <div class="text-wrap text-center">Tidak ada desa baru yang memasang OpenSID hari ini</div>
          @endforelse
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
      height: 600px;
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
        animation: marquee-vertical 50s linear infinite;        
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