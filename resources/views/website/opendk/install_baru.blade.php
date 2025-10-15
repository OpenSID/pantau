<img src="{{ asset('assets/img/opensid_logo.png') }}" width="20" alt="Logo">
<span class="text-black">Daftar Kecamatan Baru Install</span>
<div id="block_install_baru">        
    <div class="container marquee" style="height: 250px;">
        <div class="track-vertical w-100 pr-4">
            <div class="flex-vertical marquee-fix" id="list_install_baru">
                <div class="text-wrap text-center">Tidak ada kecamatan baru yang memasang OpenDK hari ini</div>
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
@push('js')
    <script>
        $(document).ready(function() {
            $('#block_install_baru').change(function() {
                const params = {
                    kode_provinsi: $('select[name=provinsi]').val(),
                    kode_kabupaten: $('select[name=kabupaten]').val(),
                    kode_kecamatan: $('select[name=kecamatan]').val(),
                }
                $.get("{{ url('api/web/install-hari-ini-opendk') }}", params, function(data) {                    

                    let list_install_baru = ''
                    if (data.installHariIni.length > 0) {
                        data.installHariIni.forEach(function(kecamatan) {
                            list_install_baru += `<div class="mt-3 w-100 block-info">                                                
                                                    <div class="d-flex justify-content-between">
                                                        <div class="text-wrap">
                                                            <p class="m-0">${kecamatan.nama_kecamatan}</p>
                                                            <p class="m-0">${kecamatan.nama_provinsi}, ${kecamatan.nama_kabupaten}
                                                            </p>
                                                            <p class="m-0">INSTALL ${kecamatan.created_at ? moment(kecamatan.created_at).format('HH:mm:ss') : ''} | versi ${kecamatan.versi}</p>
                                                        </div>
                                                        <div class="p-0"><span
                                                                class="badge badge-success text-wrap">${kecamatan.created_at_format_human}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <hr>`
                        })
                    } else {
                        list_install_baru = '<div class="text-wrap text-center">Tidak ada kecamatan baru yang memasang OpenDK hari ini</div>'
                    }
                    $('#list_install_baru').html(list_install_baru)
                }, 'json')
            })
        })
    </script>
@endpush
